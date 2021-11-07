<?php

namespace App\Http\Requests\Frontman;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class FrontmanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'location' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'unique' => Rule::unique('frontmen', 'location')
                    ->where('team_id', current_team()->id),
            ],
        ];
    }
}
