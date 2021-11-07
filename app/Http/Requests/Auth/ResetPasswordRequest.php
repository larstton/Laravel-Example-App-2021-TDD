<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token'    => ['required'],
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
