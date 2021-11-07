<?php

namespace App\Http\Loophole\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityLogRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user'   => [
                'required',
                Rule::exists('users', 'id'),
            ],
            'team'   => [
                'required',
                Rule::exists('teams', 'id'),
            ],
            'action' => [
                'required',
            ],
        ];
    }
}
