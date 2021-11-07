<?php

namespace App\Http\Requests\SnmpCheck;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSnmpCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'preset'        => [
                'sometimes',
                'string',
                Rule::in([
                    'basedata', 'bandwidth',
                ]),
            ],
            'checkInterval' => [
                'sometimes',
                'integer',
                'min:'.current_team()->min_check_interval ?? '60',
                'max:3600',
            ],
            'active'        => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
