<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostData;
use App\Exceptions\HostException;
use App\Support\Validation\BanList;

class BannedGuard
{
    public function __invoke(HostData $hostData): void
    {
        if (is_null($hostData->connect)) {
            return;
        }

        throw_if(BanList::isConnectBanned($hostData->connect),
            HostException::bannedConnectProvided($hostData->connect)
        );
    }
}
