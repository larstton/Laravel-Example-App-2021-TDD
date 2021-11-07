<?php

namespace App\Models;

use App\Http\Transformers\DateTransformer;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class HostSummary implements Arrayable
{
    private Host $host;

    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    public function toArray()
    {
        $lastCheck = $this->host->getLastCheckTime();

        return [
            'checksCount'          => $this->host->check_count_total,
            'checksCountBreakdown' => [
                'cagentCheck'   => $this->host->agent_check_count,
                'customChecks'  => $this->host->custom_checks_count,
                'serviceChecks' => $this->host->service_checks_count,
                'snmpChecks'    => $this->host->snmp_checks_count,
                'total'         => $this->host->check_count_total,
                'webChecks'     => $this->host->web_checks_count,
            ],
            'hasIcmpCheck'         => (bool) $this->host->has_icmp_check,
            'metrics'              => $this->host->cagent_metrics,
            'state'                => $this->getState($lastCheck),
            'dates'                => [
                'lastCheckedAt' => DateTransformer::transform($lastCheck),
            ],
        ];
    }

    private function getState(?Carbon $lastCheck)
    {
        // If last check was within the hour then consider monitored.
        if (! is_null($lastCheck) && $lastCheck->greaterThan(now()->subHour())) {
            return 'MONITORED';
        }

        return 'PENDING';
    }
}
