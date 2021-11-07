<?php

namespace App\Http\Requests\Frontman;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateFrontmanRequest extends FrontmanRequest
{
    public function rules()
    {
        $rules = parent::rules();

        Arr::set(
            $rules,
            'location.unique',
            Arr::get($rules, 'location.unique')->ignore($this->route('frontman'))
        );

        return $rules;
    }
}
