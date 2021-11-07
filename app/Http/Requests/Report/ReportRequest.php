<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'filter.from' => [
                'required',
                'integer',
            ],
            'filter.to'   => [
                'required',
                'integer',
            ],
            'filter.host'        => [
                'sometimes',
                'nullable',
                'string',
                'uuid',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'filter.from' => 'from date',
            'filter.to'   => 'to date',
        ];
    }
}
