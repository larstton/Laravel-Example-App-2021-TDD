<?php

namespace App\Http\Resources\Report;

use App\Http\Resources\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'hostName' => $this['name'] ?? null,
            'hostId'   => $this['host_id'] ?? null,
            'check'    => $this['check'] ?? null,
            'uuid'     => $this['uuid'] ?? null,
            'issue'    => $this['issue'] ?? null,
            'severity' => $this['severity'] ?? null,
            'time'     => $this['time'] ?? null,
            'percent'  => $this['percent'] ?? 0,
        ];
    }
}
