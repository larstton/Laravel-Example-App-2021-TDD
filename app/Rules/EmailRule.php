<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailRule implements Rule
{
    public function passes($attribute, $value)
    {
        $rules = 'email:rfc';
        if (! app()->environment('local')) {
            $rules .= ',dns';
        }

        return (bool) validator([$attribute => $value], [
            $attribute => $rules,
        ])->validate();
    }

    public function message()
    {
        return 'You must provide a real functioning email address.';
    }
}
