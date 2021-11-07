<?php

namespace App\Rules;

use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;
use Illuminate\Contracts\Validation\Rule;

class ValidPublicConnectRule implements Rule
{
    public function passes($attribute, $value)
    {
        return IpAddress::isValidPublicIP($value) || FQDN::isValidPublicFQDN($value);
    }

    public function message()
    {
        return 'The provided :attribute (:input) is not a valid public connect.';
    }
}
