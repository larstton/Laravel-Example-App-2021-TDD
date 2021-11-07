<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Support\LatestData\LatestData;

/**
 * @mixin LatestData
 */
class HostLatestDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'totalChecksCount' => $this->totalChecksCount,
            'cagent'           => [
                'dataUpdatedAt'           => DateTransformer::transform($this->agentDataUpdatedAt),
                'osMetrics'               => $this->when(
                    $this->osMetrics->isNotEmpty(),
                    $this->osMetrics
                ),
                'processes'               => $this->when(
                    $this->processes->isNotEmpty(),
                    $this->processes
                ),
                'services'                => $this->when(
                    $this->services->isNotEmpty(),
                    $this->services
                ),
                'modules'                 => $this->when(
                    $this->modules->isNotEmpty(),
                    $this->modules
                ),
                'smartmon'                => $this->when(
                    $this->smartMon->isNotEmpty(),
                    $this->smartMon
                ),
                'temperatures'            => $this->when(
                    $this->temperatures->isNotEmpty(),
                    $this->temperatures
                ),
                'listeningports'          => $this->when(
                    $this->listeningports->isNotEmpty(),
                    $this->listeningports
                ),
                'inventory'               => $this->when(
                    $this->inventory->isNotEmpty(),
                    $this->inventory
                ),
                $this->mergeWhen(count($this->additionalData), $this->additionalData),
                'cpuUtilisationSnapshots' => [
                    'available' => $this->cpuUtilisationSnapshotsCount,
                    'links'     => $this->when($this->cpuUtilisationSnapshotsCount, [
                        'follow' => route('engine.host.cpu-utilisation-snapshots', $this->hostId),
                    ]),
                ],
                'jobmonResults'           => [
                    'available' => $this->jobmonResultsCount,
                    'links'     => $this->when($this->jobmonResultsCount, [
                        'follow' => route('engine.host.jobmon-results', $this->hostId),
                    ]),
                ],
                'hwInventory'             => $this->when($this->hwInventory->isNotEmpty(), [
                    'dataUpdatedAt' => DateTransformer::transform($this->hwInventoryUpdatedAt),
                    'hwInventory'   => $this->hwInventory,
                ]),
            ],
            'webChecks'        => $this->when($this->webChecks->isNotEmpty(), $this->webChecks),
            'serviceChecks'    => $this->when($this->serviceChecks->isNotEmpty(), $this->serviceChecks),
            'customChecks'     => $this->when($this->customChecks->isNotEmpty(), $this->customChecks),
            'snmpChecks'       => $this->when($this->snmpChecks->isNotEmpty(), $this->snmpChecks),
        ];
    }
}
