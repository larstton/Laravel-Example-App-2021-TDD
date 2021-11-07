<?php

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace App\Data\Rule;

use App\Data\BaseData;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Http\Requests\Rule\RuleRequest;
use Illuminate\Support\Arr;

class RuleData extends BaseData
{
    public RuleHostMatchPart $hostMatchPart;
    public string $hostMatchCriteria;
    /**
     * @var null|\App\Enums\Rule\RuleCheckType[]
     */
    public $checkType;
    public string $checkKey;
    public ?array $keyFunction;
    public ?RuleFunction $function;
    public ?RuleOperator $operator;
    public ?int $resultsRange;
    public ?float $threshold;
    public ?RuleThresholdUnit $unit;
    public bool $active;
    public ?RuleAction $action;
    public bool $finish;
    public ?string $expressionAlias;

    public static function fromRequest(RuleRequest $request)
    {
        return new self([
            'hostMatchPart'     => RuleHostMatchPart::coerce($request->hostMatchPart),
            'hostMatchCriteria' => $request->hostMatchCriteria,
            'checkType'         => self::setCheckType($request),
            'checkKey'          => $request->checkKey,
            'keyFunction'       => self::nullableJsonCast($request->keyFunction),
            'function'          => RuleFunction::coerce($request->function),
            'operator'          => RuleOperator::coerce($request->operator),
            'resultsRange'      => self::nullableIntCast($request->resultsRange),
            'threshold'         => (float) $request->threshold,
            'unit'              => RuleThresholdUnit::coerce($request->unit),
            'active'            => (bool) $request->active,
            'action'            => RuleAction::coerce($request->action),
            'finish'            => (bool) $request->finish,
            'expressionAlias'   => $request->expressionAlias,
        ]);
    }

    public static function setCheckType(RuleRequest $request): ?array
    {
        if (is_null($request->checkType)) {
            return null;
        }

        $checkTypes = [];
        foreach (Arr::wrap($request->checkType) as $checkType) {
            $checkTypes[] = RuleCheckType::coerce($checkType);
        }

        return $checkTypes;
    }
}
