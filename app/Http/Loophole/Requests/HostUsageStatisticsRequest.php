<?php

namespace App\Http\Loophole\Requests;

use App\Rules\PastOrPresentDateRule;
use Illuminate\Foundation\Http\FormRequest;

class HostUsageStatisticsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'period' => [
                'sometimes',
                'nullable',
                'date_format:Y-m',
                new PastOrPresentDateRule('Y-m'),
            ],
        ];
    }
}
