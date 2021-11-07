<?php

namespace App\Http\Requests\ApiToken;

use App\Enums\ApiTokenCapability;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateApiTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'       => [
                'required',
                'string',
                'min:3',
                'max:30',
                Rule::unique('api_tokens')->where('team_id', current_team()->id),
            ],
            'capability' => [
                'required',
                new EnumValue(ApiTokenCapability::class),
            ],
        ];
    }
}
