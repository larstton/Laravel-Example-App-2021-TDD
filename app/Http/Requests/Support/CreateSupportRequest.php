<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'body'         => [
                'required',
                'string',
                'min:5',
                'max:10000',
            ],
            'subject'      => [
                'required',
                'string',
                'min:5',
                'max:999',
            ],
            'attachment.*' => [
                'sometimes',
                'file',
                'mimes:jpg,jpeg,JPG,png,pdf',
                'max:10000',
            ],
        ];
    }
}
