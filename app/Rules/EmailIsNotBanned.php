<?php

namespace App\Rules;

use App\Support\Validation\BanList;
use Illuminate\Contracts\Validation\Rule;

class EmailIsNotBanned implements Rule
{
    public function passes($attribute, $value)
    {
        return ! BanList::isEmailBanned($value);
    }

    public function message()
    {
        return 'The email ":input" is not allowed.';
    }
}
