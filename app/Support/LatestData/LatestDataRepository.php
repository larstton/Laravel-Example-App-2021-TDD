<?php

namespace App\Support\LatestData;

use App\Enums\CheckType;
use App\Models\CheckResult;
use App\Models\Host;
use Illuminate\Support\Collection;

class LatestDataRepository
{
    private Host $host;
    private LatestData $latestData;

    public function __construct(Host $host)
    {
        $this->host = $host;
        $this->latestData = new LatestData($this->host);
    }

    public function build(): LatestData
    {
        $this->latestData->addAgentData($this->fetchAgentData());
        $this->latestData->addWebChecks($this->fetchHostChecksByType(CheckType::WebCheck()));
        $this->latestData->addServiceChecks($this->fetchHostChecksByType(CheckType::ServiceCheck()));
        $this->latestData->addSnmpChecks($this->fetchHostChecksByType(CheckType::SnmpCheck()));
        $this->latestData->addCustomChecks($this->fetchHostChecksByType(CheckType::CustomCheck()));

        return $this->latestData;
    }

    private function fetchAgentData(): ?CheckResult
    {
        return $this->host
            ->agentCheckResults()
            ->latest('id')
            ->first(['data', 'data_updated_at']);
    }

    private function fetchHostChecksByType(CheckType $type): Collection
    {
        return CheckResult::hostChecksByType($this->host, $type)->get();
    }
}
