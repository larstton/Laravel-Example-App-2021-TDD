<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamMemberRegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'           => [
                'required',
                new EmailRule,
                Rule::exists('users', 'email'),
            ],
            'password'        => [
                'required',
                'string',
                'min:7',
            ],
            'termsAccepted'   => [
                'required',
                'accepted',
            ],
            'privacyAccepted' => [
                'required',
                'accepted',
            ],
            'signature'       => [
                'required',
                'string',
            ],
            'nickname'        => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'termsAccepted'   => 'terms & conditions',
            'privacyAccepted' => 'privacy policy',
        ];
    }
}
