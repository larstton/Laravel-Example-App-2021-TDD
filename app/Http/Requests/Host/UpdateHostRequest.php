<?php

namespace App\Http\Requests\Host;

use Illuminate\Support\Arr;

class UpdateHostRequest extends HostRequest
{
    public function rules()
    {
        $rules = parent::rules();

        Arr::set(
            $rules,
            'name.unique',
            Arr::get($rules, 'name.unique')->ignore($this->route('host'))
        );
        Arr::set(
            $rules,
            'connect.unique',
            Arr::get($rules, 'connect.unique')->ignore($this->route('host'))
        );

        return $rules;
    }
}
