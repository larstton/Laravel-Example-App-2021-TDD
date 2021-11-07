<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'    => [
                'required',
                new EmailRule,
            ],
            'password' => [
                'required',
                'string',
                'min:7',
            ],
        ];
    }
}
