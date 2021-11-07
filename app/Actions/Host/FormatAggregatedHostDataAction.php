<?php

namespace App\Actions\Host;

use App\Data\Host\AggregatedHostDataData;
use App\Models\Host;
use App\Models\SubUnit;
use App\Models\Tag;

abstract class FormatAggregatedHostDataAction
{
    /**
     * @param  Tag|SubUnit  $entity
     * @param  array|string  $groupedEntity
     * @param  string  $groupBy
     * @return AggregatedHostDataData
     */
    protected function collateAndTransform(
        $entity,
        $groupedEntity,
        string $groupBy
    ): AggregatedHostDataData {
        $aggregatedData = [
            'groupedBy'          => $groupBy,
            'groupedEntity'      => $groupedEntity,
            'hostsCount'         => $entity->hosts->count(),
            'checkCount'         => 0,
            'connectedAgents'    => 0,
            'disconnectedAgents' => 0,
            'metrics'            => 0,
            'alerts'             => $this->getTotalAlertCount($entity),
            'warnings'           => $this->getTotalWarningCount($entity),
        ];

        $entity->hosts->map(function (Host $host) use (&$aggregatedData) {
            $aggregatedData['checkCount'] += $host->total_checks_count;
            if (optional($host->cagent_last_updated_at)->greaterThan(now()->subHour())) {
                $aggregatedData['connectedAgents']++;
            } elseif (! is_null($host->cagent_last_updated_at)) {
                $aggregatedData['disconnectedAgents']++;
            }
            $aggregatedData['metrics'] += $host->cagent_metrics;
        });

        return AggregatedHostDataData::make($aggregatedData);
    }

    private function getTotalAlertCount($entity): int
    {
        return $entity->hosts->sum->alert_count;
    }

    private function getTotalWarningCount($entity): int
    {
        return $entity->hosts->sum->warning_count;
    }
}
