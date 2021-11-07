<?php

namespace App\Http\Requests\SubUnit;

use Illuminate\Support\Arr;

class UpdateSubUnitRequest extends SubUnitRequest
{
    public function rules()
    {
        $rules = parent::rules();

        Arr::set(
            $rules,
            'shortId.unique',
            Arr::get($rules, 'shortId.unique')->ignore($this->route('sub_unit'))
        );

        return $rules;
    }
}
