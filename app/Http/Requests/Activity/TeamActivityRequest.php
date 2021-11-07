<?php

namespace App\Http\Requests\Activity;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TeamActivityRequest extends FormRequest
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
