<?php

namespace App\Http\Requests\SubUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class SubUnitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'shortId'     => [
                'required',
                'string',
                'alpha_dash',
                'min:2',
                'max:20',
                'unique' => Rule::unique('sub_units', 'short_id')
                    ->where('team_id', current_team()->id),
            ],
            'name'        => [
                'nullable',
                'string',
                'min:1',
                'max:150',
            ],
            'information' => [
                'nullable',
                'string',
                'min:1',
                'max:5000',
            ],
        ];
    }
}
