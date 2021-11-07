<?php

namespace App\Http\Requests\CustomCheck;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

abstract class CustomCheckRequest extends FormRequest
{
    public function messages()
    {
        $pattern = Str::of(collect($this->rules()['name'])
            ->first(fn ($value) => Str::startsWith($value, 'regex')))
            ->match('/\[(.*)\]/');

        return [
            'name.regex' => "The custom check name can only include the following characters ({$pattern})",
        ];
    }

    public function rules()
    {
        return [
            'name'                   => [
                'required',
                'string',
                'min:3',
                'max:25',
                'regex:/^[a-zA-Z0-9]+$/',
                'unique' => Rule::unique('custom_checks', 'name')
                    ->where('host_id', $this->route('host')->id),
            ],
            'expectedUpdateInterval' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) $value !== 0 && (int) $value < 90) {
                        $fail('Expected update interval must be 0, or >= 90 seconds');
                    }
                },
                'max:723540',
            ],
        ];
    }
}
