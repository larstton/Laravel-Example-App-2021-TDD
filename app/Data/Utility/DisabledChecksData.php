<?php

namespace App\Data\Utility;

use App\Models\ServiceCheck;
use App\Models\WebCheck;

class DisabledChecksData
{
    public array $webChecks = [];
    public array $serviceChecks = [];

    public function addWebCheck(WebCheck $webCheck)
    {
        $this->webChecks[] = $webCheck;
    }

    public function addServiceCheck(ServiceCheck $serviceCheck)
    {
        $this->serviceChecks[] = $serviceCheck;
    }

    public function getChecks()
    {
        return [
            'web'     => $this->webChecks,
            'service' => $this->serviceChecks,
        ];
    }
}
