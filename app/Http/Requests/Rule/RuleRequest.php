<?php

namespace App\Http\Requests\Rule;

use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Rules\CombineableCheckTypeRule;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RuleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'hostMatchPart'     => [
                'required',
                'string',
                new EnumValue(RuleHostMatchPart::class),
            ],
            'hostMatchCriteria' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'checkType'         => [
                'array',
                'min:1',
                new CombineableCheckTypeRule,
            ],
            'checkType.*'       => [
                'string',
                new EnumValue(RuleCheckType::class),
            ],
            'checkKey'          => [
                'required',
                'min:3',
                'max:150',
            ],
            'keyFunction'       => [
                'nullable',
                'json',
            ],
            'function'          => [
                'nullable',
                'string',
                new EnumValue(RuleFunction::class),
            ],
            'operator'          => [
                'nullable',
                'string',
                new EnumValue(RuleOperator::class),
            ],
            'resultsRange'      => [
                'nullable',
                'sometimes',
                'integer',
                'min:0',
                'max:99',
            ],
            'threshold'         => [
                'nullable',
                'numeric',
            ],
            'unit'              => [
                'nullable',
                new EnumValue(RuleThresholdUnit::class),
            ],
            'active'            => [
                'required',
                'bool',
            ],
            'action'            => [
                'string',
                new EnumValue(RuleAction::class),
            ],
            'finish'            => [
                'required',
                'bool',
            ],
            'expressionAlias'   => [
                'nullable',
                'string',
                'regex:/failed_[0-9]+_times/',
            ],
        ];
    }

    public function messages()
    {
        return [
            'hostMatchPart.in' => 'Ignore rules are only allowed for single host.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('hostMatchPart', Rule::in(RuleHostMatchPart::UUID), function ($input) {
            return $input->action === RuleAction::Ignore;
        });
    }
}
