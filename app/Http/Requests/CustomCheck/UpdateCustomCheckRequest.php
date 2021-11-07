<?php

namespace App\Http\Requests\CustomCheck;

use Illuminate\Support\Arr;

class UpdateCustomCheckRequest extends CustomCheckRequest
{
    public function rules()
    {
        $rules = parent::rules();

        Arr::set(
            $rules,
            'name.unique',
            Arr::get($rules, 'name.unique')->ignore($this->route('custom_check'))
        );

        return $rules;
    }
}
