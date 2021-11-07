<?php

namespace App\Http\Requests\SnmpCheck;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSnmpCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'preset'        => [
                'required',
                'string',
                Rule::in([
                    'basedata', 'bandwidth',
                ]),
            ],
            'checkInterval' => [
                'required',
                'integer',
                'min:'.current_team()->min_check_interval ?? '60',
                'max:3600',
            ],
            'active'        => [
                'required',
                'boolean',
            ],
        ];
    }
}
