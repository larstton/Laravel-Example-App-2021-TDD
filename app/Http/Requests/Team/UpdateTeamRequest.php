<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'            => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:200',
            ],
            'timezone'        => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:200',
                'timezone',
            ],
            'defaultFrontman' => [
                'sometimes',
                'nullable',
                'uuid',
                Rule::exists('frontmen', 'id'),
            ],
            'dateFormat'      => [
                'sometimes',
                'nullable',
                'string',
                'regex:/[LBM]./',
            ],
            'hasGrantedAccessToSupport' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }
}
