<?php

namespace App\Models;

use App\Casts\RuleCheckTypeCast;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Events\Rule\RuleCreated;
use App\Events\Rule\RuleDeleted;
use App\Events\Rule\RuleUpdated;
use App\Exceptions\RuleException;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use App\Models\Concerns\PurgesCache;
use App\Support\Rule\JsonFunctionRuleNameBuilder;
use App\Support\Rule\PendingRule;
use App\Support\Rule\SimpleRuleNameBuilder;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @mixin IdeHelperRule
 */
class Rule extends BaseModel implements Sortable
{
    use SortableTrait, OwnedByTeam, CastsEnums, LogsActivity, PurgesCache;

    public $sortable = [
        'order_column_name'  => 'position',
        'sort_when_creating' => true,
    ];

    protected $dispatchesEvents = [
        'created' => RuleCreated::class,
        'updated' => RuleUpdated::class,
        'deleted' => RuleDeleted::class,
    ];

    protected $casts = [
        'key_function'  => 'array',
        'check_type'    => RuleCheckTypeCast::class,
        'active'        => 'bool',
        'finish'        => 'bool',
        'mandatory'     => 'bool',
        'results_range' => 'int',
    ];

    protected $enumCasts = [
        'action'          => RuleAction::class,
        'function'        => RuleFunction::class,
        'operator'        => RuleOperator::class,
        'host_match_part' => RuleHostMatchPart::class,
        'unit'            => RuleThresholdUnit::class,
    ];

    public static function newRuleForTeam(Team $team): PendingRule
    {
        return (new PendingRule(self::makeBaseRule()))->forTeam($team);
    }

    /**
     * The Rule created here is used as the base for all RuleFactory methods.
     *
     * @return Rule
     */
    public static function makeBaseRule(): self
    {
        return new self([
            'key_function'        => ['key' => '', 'value' => ''],
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Alert(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
        ]);
    }

    protected static function booted()
    {
        static::saving(function (self $rule) {
            if (is_null($rule->checksum)) {
                $rule->calculateChecksum();
            }

            $exists = self::where('checksum', $rule->checksum)
                ->where('id', '!=', $rule->id)
                ->exists();

            throw_if($exists, RuleException::duplicateRuleNotAllowed());

            return true;
        });
    }

    public function calculateChecksum()
    {
        $this->checksum = (string) str(collect($this->getAttributes())->only([
            'team_id',
            'host_match_criteria',
            'host_match_part',
            'check_type',
            'check_key',
            'key_function',
            'action',
            'results_range',
            'function',
            'operator',
            'threshold',
            'unit',
            'expression_alias',
        ])->map(function ($value, $key) {
            if ($key !== 'unit') {
                return $value;
            }

            $unit = RuleThresholdUnit::coerce($value);
            if (optional($unit)->in([RuleThresholdUnit::Byte(), RuleThresholdUnit::Second()])) {
                return null;
            }

            return $value;
        })->sortKeys()->implode(''))->trim()->replaceMatches('/\s+/', '')->toMd5();
    }

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateExistingRule()
    {
        return new PendingRule($this);
    }

    public function buildSortQuery()
    {
        return static::query()->where('team_id', $this->team_id);
    }

    public function purgeableEvents(): array
    {
        return [
            'created' => 'rules-team-'.$this->team_id,
            'updated' => 'rules-team-'.$this->team_id,
            'deleted' => 'rules-team-'.$this->team_id,
        ];
    }

    protected function setActivityLogAction(string $eventName): string
    {
        if (Str::contains($this->check_key, '@')) {
            $nameBuilder = new JsonFunctionRuleNameBuilder($this);
        } else {
            $nameBuilder = new SimpleRuleNameBuilder($this);
        }

        return sprintf('Rule "%s" %s', Str::limit($nameBuilder->build(), 240), $eventName);
    }
}
