<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;

class HostConnectValidTypeCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'connect' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['connect' => $this->route('connect')]);
    }
}
