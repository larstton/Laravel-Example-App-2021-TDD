<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostData;
use App\Exceptions\HostException;
use App\Models\Host;

class CompatibilityGuard
{
    public function __invoke(HostData $hostData, ?Host $host): void
    {
        if (is_null(optional($host)->connect)) {
            return;
        }

        $connectBeingRemoved = $host->connect && ! $hostData->connect;
        $hostHasChecks = $host->serviceChecks()->exists() || $host->webChecks()->exists();
        throw_if($connectBeingRemoved && $hostHasChecks,
            HostException::hostWithServiceOrWebChecksCannotHaveEmptyConnect()
        );
    }
}
