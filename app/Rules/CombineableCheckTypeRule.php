<?php

namespace App\Rules;

use App\Enums\Rule\RuleCheckType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class CombineableCheckTypeRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (count($value) === 1) {
            return true;
        }

        $value = collect($value);

        return collect(RuleCheckType::combineableCheckTypes())
            ->first(fn ($combinables) => $value->every(fn ($check) => Str::is($combinables, $check)));
    }

    public function message()
    {
        return 'These check types are not combineable.';
    }
}
