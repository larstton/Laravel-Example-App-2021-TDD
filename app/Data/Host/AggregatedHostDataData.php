<?php

namespace App\Data\Host;

use App\Data\BaseData;

class AggregatedHostDataData extends BaseData
{
    public string $groupedBy;

    /**
     * @var string|array
     */
    public $groupedEntity;

    public int $hostsCount;

    public int $checkCount;

    public int $connectedAgents;

    public int $disconnectedAgents;

    public int $metrics;

    public int $alerts;

    public int $warnings;

    public static function make(array $aggregatedData): self
    {
        return new self([
            'groupedBy'          => $aggregatedData['groupedBy'],
            'groupedEntity'      => $aggregatedData['groupedEntity'],
            'hostsCount'         => (int) $aggregatedData['hostsCount'],
            'checkCount'         => (int) $aggregatedData['checkCount'],
            'connectedAgents'    => (int) $aggregatedData['connectedAgents'],
            'disconnectedAgents' => (int) $aggregatedData['disconnectedAgents'],
            'metrics'            => (int) $aggregatedData['metrics'],
            'alerts'             => (int) $aggregatedData['alerts'],
            'warnings'           => (int) $aggregatedData['warnings'],
        ]);
    }
}
