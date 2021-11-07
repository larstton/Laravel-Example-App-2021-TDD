<?php

namespace App\Http\Resources\Host;

use App\Data\Host\AggregatedHostDataData;
use App\Http\Resources\JsonResource;

/**
 * @mixin AggregatedHostDataData
 */
class AggregatedHostDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'groupedBy'          => $this->groupedBy,
            'groupedEntity'      => $this->groupedEntity,
            'numberOfHosts'      => $this->hostsCount,
            'numberOfChecks'     => $this->checkCount,
            'numberOfMetrics'    => $this->metrics,
            'alerts'             => $this->alerts,
            'warnings'           => $this->warnings,
            'connectedAgents'    => $this->connectedAgents,
            'disconnectedAgents' => $this->disconnectedAgents,
        ];
    }
}
