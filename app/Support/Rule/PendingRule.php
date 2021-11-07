<?php

namespace App\Support\Rule;

use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Models\Concerns\AuthedEntity;
use App\Models\Host;
use App\Models\Rule;
use App\Models\Team;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PendingRule
{
    protected Rule $rule;

    private $checkTypes = [];

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    public function forTeam(Team $team)
    {
        $this->rule->team_id = $team->id;

        return $this;
    }

    public function createdByAuthedEntity(AuthedEntity $authedEntity)
    {
        $this->rule->user_id = $authedEntity->getAuthIdentifier();

        return $this;
    }

    public function withWebCheck()
    {
        return $this->withCheckTypes(RuleCheckType::WebCheck());
    }

    /**
     * @param  null|RuleCheckType|RuleCheckType[]  $types
     * @return $this
     */
    public function withCheckTypes($types)
    {
        if (is_null($types)) {
            return $this;
        }

        array_push($this->checkTypes, ...Arr::wrap($types));

        $this->rule->check_type = $this->checkTypes;

        return $this;
    }

    public function withAgentCheck()
    {
        return $this->withCheckTypes(RuleCheckType::Cagent());
    }

    public function withServiceCheck()
    {
        return $this->withCheckTypes(RuleCheckType::ServiceCheck());
    }

    public function withCustomCheck()
    {
        return $this->withCheckTypes(RuleCheckType::CustomCheck());
    }

    public function setActiveState(bool $value)
    {
        $this->rule->active = $value ?? $this->rule->active;

        return $this;
    }

    public function setMandatoryState(bool $value)
    {
        $this->rule->mandatory = $value ?? $this->rule->mandatory;

        return $this;
    }

    public function setFinishState(bool $value)
    {
        $this->rule->finish = $value ?? $this->rule->finish;

        return $this;
    }

    public function withLastFunction()
    {
        return $this->setFunction(RuleFunction::Last());
    }

    public function setFunction(?RuleFunction $function)
    {
        $this->rule->function = $function;

        return $this;
    }

    public function withAverageFunction()
    {
        return $this->setFunction(RuleFunction::Average());
    }

    public function withWarningAction()
    {
        return $this->setAction(RuleAction::Warn());
    }

    public function setAction(?RuleAction $action)
    {
        $this->rule->action = $action ?? $this->rule->action;

        return $this;
    }

    public function setKeyFunction($value)
    {
        $this->rule->key_function = $value ?? $this->rule->key_function;

        return $this;
    }

    public function setCheckKey(string $checkKey)
    {
        $this->rule->check_key = $checkKey;

        return $this;
    }

    public function setHostMatchPart(RuleHostMatchPart $hostMatchPart)
    {
        $this->rule->host_match_part = $hostMatchPart;

        return $this;
    }

    public function setHostMatchCriteria(string $criteria)
    {
        $this->rule->host_match_criteria = $criteria;

        return $this;
    }

    public function withExpressionAlias(?string $expressionAlias)
    {
        if (is_null($expressionAlias)) {
            return $this;
        }

        if (preg_match('/failed_([0-9]+)_times/', $expressionAlias, $match)) {
            $this->rule->expression_alias = $expressionAlias;
            $this->rule->results_range = $match[1];
            $this->rule->function = RuleFunction::Sum();
            $this->rule->operator = RuleOperator::EqualTo();
            $this->rule->threshold = 0;
        }

        return $this;
    }

    public function withLessThanOperator()
    {
        return $this->setOperator(RuleOperator::LessThan());
    }

    public function setOperator(?RuleOperator $operator)
    {
        $this->rule->operator = $operator;

        return $this;
    }

    public function withGreaterThanOperator()
    {
        return $this->setOperator(RuleOperator::GreaterThan());
    }

    public function withNotEmptyOperator()
    {
        return $this->setOperator(RuleOperator::NotEmpty());
    }

    public function setThreshold(?float $threshold)
    {
        $this->rule->threshold = $threshold;

        return $this;
    }

    public function setUnit(?RuleThresholdUnit $unit)
    {
        $this->rule->unit = $unit;

        return $this;
    }

    public function setResultsRange(?int $resultsRange)
    {
        $this->rule->results_range = $resultsRange;

        return $this;
    }

    /**
     * @param  Host|null  $host
     * @return Rule|bool
     */
    public function saveIfNew(?Host $host = null)
    {
        $rule = $this->getRule();

        if (is_null($rule->checksum)) {
            $rule->calculateChecksum();
        }

        $shouldCreate = Rule::withoutGlobalScope('team')
            ->where('rules.team_id', $rule->team_id)
            ->where('checksum', $rule->checksum)
            ->when($host, function (Builder $query) use ($host) {
                $query->join('hosts', 'hosts.team_id', '=', 'rules.team_id')
                    ->where('hosts.id', $host->id);
            })
            ->doesntExist();

        if ($shouldCreate) {
            return $this->save();
        }

        return false;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function save(): Rule
    {
        /* @var Rule $this */
        return tap($this->getRule())->save();
    }

    public function updateWithoutClash()
    {
        $rule = $this->getRule();

        $rule->calculateChecksum();

        $shouldUpdate = Rule::where('checksum', $rule->checksum)
            ->where('id', '!=', $rule->id)
            ->doesntExist();

        if ($shouldUpdate) {
            return $this->save();
        }

        return false;
    }

    /**
     * @param $condition Closure|bool
     * @return bool
     */
    public function deleteIf($condition): bool
    {
        if (value($condition)) {
            return $this->deleteIfExists();
        }

        return false;
    }

    public function deleteIfExists(): bool
    {
        $this->rule->calculateChecksum();

        return (bool) optional(
            Rule::where('checksum', $this->rule->checksum)->first()
        )->delete();
    }
}
