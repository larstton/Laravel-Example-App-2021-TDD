<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostData;
use App\Exceptions\HostException;
use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;

class ConnectGuard
{
    public function __invoke(HostData $hostData): void
    {
        if (is_null($hostData->connect)) {
            return;
        }

        if ($hostData->frontman || $hostData->cagent) {
            throw_unless(
                IpAddress::isValid($hostData->connect) || FQDN::isValid($hostData->connect),
                HostException::invalidConnectProvided($hostData->connect)
            );
        } else {
            throw_unless(
                IpAddress::isValidPublicIP($hostData->connect)
                || FQDN::isValidPublicFQDN($hostData->connect),
                HostException::invalidPublicConnectProvided($hostData->connect)
            );
        }
    }
}
