<?php

namespace Database\Factories;

use App\Data\Rule\RuleData;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use Illuminate\Foundation\Testing\WithFaker;

class RuleDataFactory
{
    use WithFaker;

    public static function make(array $params = []): RuleData
    {
        $faker = (new self)->makeFaker();

        return new RuleData(array_merge([
            'hostMatchPart'     => RuleHostMatchPart::None(),
            'hostMatchCriteria' => 'any',
            'checkType'         => [
                RuleCheckType::ServiceCheck(),
            ],
            'checkKey'          => '*.success',
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => RuleFunction::Average(),
            'operator'          => RuleOperator::LessThan(),
            'resultsRange'      => 5,
            'threshold'         => 10.0,
            'unit'              => null,
            'active'            => true,
            'action'            => RuleAction::Alert(),
            'finish'            => false,
            'expressionAlias'   => null,
        ], $params));
    }
}
