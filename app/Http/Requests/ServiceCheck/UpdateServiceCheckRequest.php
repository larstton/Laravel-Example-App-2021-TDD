<?php

namespace App\Http\Requests\ServiceCheck;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'checkInterval' => [
                'required',
                'integer',
                'min:'.current_team()->min_check_interval ?? '60',
                'max:3600',
            ],
            'active'        => [
                'required',
                'boolean',
            ],
        ];
    }
}
