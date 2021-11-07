<?php

namespace App\Http\Requests\StatusPage;

use Illuminate\Foundation\Http\FormRequest;

class StatusPageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title'             => [
                'required',
                'string',
                'max:150',
            ],
            'meta'              => [
                'required',
                'array',
            ],
            'meta.header'       => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
            'meta.footer'       => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
            'meta.hostTags'     => [
                'sometimes',
                'nullable',
                'array',
            ],
            'meta.hostTags.*'   => [
                'string',
            ],
            'meta.history'      => [
                'sometimes',
                'nullable',
                'integer',
                'max:30',
            ],
            'meta.groupByTag'   => [
                'sometimes',
                'nullable',
                'boolean',
            ],
            'meta.showWarnings' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }
}
