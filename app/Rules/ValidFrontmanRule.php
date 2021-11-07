<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFrontmanRule implements Rule
{
    public function passes($attribute, $value)
    {
        // is connect is private, then frontman must be private too.
        // if connect is public then frontman can be either.
    }

    public function message()
    {
        return 'The validation error message.';
    }
}
