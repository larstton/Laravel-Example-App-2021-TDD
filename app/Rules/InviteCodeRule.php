<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class InviteCodeRule implements Rule
{
    private $passableTrialDurations = [30, 45, 60, 75, 90];

    public function passes($attribute, $value)
    {
        if (! preg_match('/[i-z][a-h]([0-9])[a-z]([1-9])([a-z][a-z])/', $value, $match)) {
            return false;
        }

        $trialDurationInDays = (int) $match[2].$match[1];

        if (! in_array($trialDurationInDays, $this->passableTrialDurations)) {
            return false;
        }

        if (! in_array($match[3], ['ap', 'ht', 'qp', 'bk', 'et'])) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'You invitation code you supplied was not valid.';
    }
}
