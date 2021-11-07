<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nickname' => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'name' => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'lang'     => [
                'sometimes',
                Rule::in(['en', 'es', 'pt', 'fr', 'de']),
            ],
        ];
    }
}
