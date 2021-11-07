<?php

namespace App\Support\Preflight\Contract;

use App\Data\ServiceCheck\ServiceCheckData;
use App\Data\WebCheck\WebCheckData;
use App\Models\Host;

interface CheckPreflight
{
    public static function serviceCheck(Host $host, ServiceCheckData $check): bool;

    public static function webCheck(Host $host, WebCheckData $check): bool;
}
