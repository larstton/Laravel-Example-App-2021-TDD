<?php

namespace App\Rules;

use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;
use Illuminate\Contracts\Validation\Rule;

class ValidPrivateConnectRule implements Rule
{
    public function passes($attribute, $value)
    {
        return IpAddress::isValidPrivate($value) || FQDN::isValidPrivate($value);
    }

    public function message()
    {
        return 'The provided :attribute (:input) is not a valid private connect.';
    }
}
