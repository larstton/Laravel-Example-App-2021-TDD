<?php

namespace App\Http\Requests\Activity;

use App\Rules\PastOrPresentDateRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class HostActivityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'filter.month' => [
                'sometimes',
                'nullable',
                'date_format:Y-m',
                new PastOrPresentDateRule('Y-m'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'filter.month' => 'date',
        ];
    }

    public function getMonthFilter(): Carbon
    {
        return Carbon::parse(request('filter.month') ?? date('Y-m'));
    }
}
