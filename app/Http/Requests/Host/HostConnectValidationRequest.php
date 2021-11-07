<?php

namespace App\Http\Requests\Host;

use App\Rules\ValidPrivateConnectRule;
use App\Rules\ValidPublicConnectRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HostConnectValidationRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'connect' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'type'    => [
                'nullable',
                'string',
                Rule::in(['private', 'public']),
            ],
        ];

        if ($this->type === 'public') {
            data_set($rules, 'connect.valid', new ValidPublicConnectRule);
        } else {
            data_set($rules, 'connect.valid', new ValidPrivateConnectRule);
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'connect' => $this->route('connect'),
            'type'    => Str::lower($this->type),
        ]);
    }
}
