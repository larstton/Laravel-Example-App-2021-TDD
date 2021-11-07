<?php

namespace App\Rules;

use App\Models\Host;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ICMPCheckUniquenessRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (Str::lower($value) !== 'icmp') {
            return true;
        }

        /** @var Host $host */
        $host = request()->route('host');

        return $host->serviceChecks()
            ->where('protocol', 'icmp')
            ->doesntExist();
    }

    public function message()
    {
        return 'This host already has an ICMP check configured.';
    }
}
