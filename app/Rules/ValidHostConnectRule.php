<?php

namespace App\Rules;

use App\Models\Frontman;
use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;
use Illuminate\Contracts\Validation\Rule;

class ValidHostConnectRule implements Rule
{
    public function passes($attribute, $value)
    {
        // If connect is empty then this custom rule will never trigger...else...
        // ...if agent is used, connect can be valid public or private...else...
        // ...if frontman provided & is private, connect can be valid public or private...else...
        // ...if frontman is public and no agent, then connect must be public.

        if (request('cagent', false)) {
            return IpAddress::isValid($value) || FQDN::isValid($value);
        }

        if ($frontmanId = request('frontmanId')) {
            if (optional(Frontman::find($frontmanId))->isPrivate()) {
                return IpAddress::isValid($value) || FQDN::isValid($value);
            }
        }

        return IpAddress::isValidPublicIP($value) || FQDN::isValidPublicFQDN($value);
    }

    public function message()
    {
        if (! request('cagent', false) && optional(Frontman::find(request('frontmanId')))->isPublic()) {
            return 'The provided :attribute (:input) does not seem to be publicly accessible, and therefore must be monitored from a private Frontman.';
        }

        return 'The provided :attribute (:input) is invalid for this host.';
    }
}
