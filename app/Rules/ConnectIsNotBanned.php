<?php

namespace App\Rules;

use App\Support\Validation\BanList;
use Illuminate\Contracts\Validation\Rule;

class ConnectIsNotBanned implements Rule
{
    public function passes($attribute, $value)
    {
        return ! BanList::isConnectBanned($value);
    }

    public function message()
    {
        return ':input domain is not allowed.';
    }
}
