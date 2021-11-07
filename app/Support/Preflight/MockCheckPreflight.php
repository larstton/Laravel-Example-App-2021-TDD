<?php

namespace App\Support\Preflight;

use App\Data\ServiceCheck\ServiceCheckData;
use App\Data\WebCheck\WebCheckData;
use App\Models\Host;
use App\Support\Preflight\Contract\CheckPreflight as CheckPreflightContract;

class MockCheckPreflight implements CheckPreflightContract
{
    public static function serviceCheck(Host $host, ServiceCheckData $check): bool
    {
        return true;
    }

    public static function webCheck(Host $host, WebCheckData $check): bool
    {
        return true;
    }
}
