<?php

namespace App\Http\Requests\User;

use App\Rules\PasswordCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'oldPassword'          => 'required|password',
            'password'             => 'required|min:6|max:30',
            'passwordConfirmation' => 'required|min:6|max:30|same:password',
        ];
    }

    public function messages()
    {
        return [
            'oldPassword.password' => 'The existing password you entered is incorrect.',
        ];
    }

    public function attributes()
    {
        return [
            'oldPassword'          => 'old password',
            'password'             => 'new password',
            'passwordConfirmation' => 'new password confirmation',
        ];
    }
}
