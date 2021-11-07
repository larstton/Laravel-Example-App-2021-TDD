<?php

namespace App\Support\LatestData;

use App\Http\Transformers\DateTransformer;
use App\Models\CheckResult;
use App\Models\Host;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as Collect;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class LatestData
{
    public Collect $osMetrics;
    public Collect $processes;
    public Collect $services;
    public Collect $modules;
    public Collect $smartMon;
    public Collect $listeningports;
    public Collect $webChecks;
    public Collect $serviceChecks;
    public Collect $snmpChecks;
    public Collect $customChecks;
    public Collect $inventory;
    public Collect $hwInventory;
    public Collect $temperatures;
    public Collect $additionalData;

    public ?Carbon $agentLastUpdatedAt;
    public ?Carbon $agentDataUpdatedAt = null;
    public ?Carbon $hwInventoryUpdatedAt = null;

    public int $cpuUtilisationSnapshotsCount;
    public int $jobmonResultsCount;
    public int $totalChecksCount;

    public string $hostId;

    public function __construct(Host $host)
    {
        $this->hostId = $host->id;
        $this->agentLastUpdatedAt = $host->cagent_last_updated_at;
        $this->totalChecksCount = $host->total_checks_count;
        $this->additionalData = collect();
        $this->hwInventory = collect();

        $this->inventory = collect($host->inventory);

        if (! is_null($host->hw_inventory) && array_key_exists('hw.inventory', $host->hw_inventory)) {
            $this->hwInventory = collect($host->hw_inventory['hw.inventory']);

            if (array_key_exists('LastUpdate', $host->hw_inventory)) {
                $this->hwInventoryUpdatedAt = Carbon::createFromTimestamp(
                    $host->hw_inventory['LastUpdate']
                );
            }
        }

        $this->cpuUtilisationSnapshotsCount = $host->cpuUtilisationSnapshots()->count();
        $this->jobmonResultsCount = $host->jobmonResults()->count();
    }

    public function addAgentData(?CheckResult $checkResult): void
    {
        $this->osMetrics = collect();
        $this->processes = collect();
        $this->services = collect();
        $this->modules = collect();
        $this->smartMon = collect();
        $this->listeningports = collect();
        $this->temperatures = collect();

        if (is_null($checkResult)) {
            return;
        }

        $agentMeasurements = collect($checkResult->data['measurements']);

        /** @var Collect $keyedData */
        /** @var Collect $osMetrics */
        [$keyedData, $osMetrics] = $agentMeasurements->partition(fn ($value) => is_array($value));

        $this->agentDataUpdatedAt = $checkResult->data_updated_at;

        $this->transformOsMetrics($osMetrics);

        $this->processes = collect($keyedData->pull('proc.list', null));
        $this->services = collect($keyedData->pull('services.list', null));
        $this->modules = collect($keyedData->pull('modules', null));
        $this->smartMon = collect($keyedData->pull('smartmon', null));
        $this->listeningports = collect($keyedData->pull('listeningports.list', null));
        $this->temperatures = collect($keyedData->pull('temperatures.list', null));

        $this->additionalData = collect();
        $keyedData->each(function ($data, $key) {
            if (Regex::match('/virt\..*\.list/U', $key)->hasMatch()) {
                $this->additionalData['hyperv'] = $this->vmlistFormatting($data);
            } elseif ($match = Regex::match('/([a-z]+)\.list$/U', $key)->groupOr(1, '')) {
                $this->additionalData[$match] = $data;
            } elseif ($match = Regex::match('/([a-z]+)\.containers$/U', $key)->groupOr(1, '')) {
                $this->additionalData[$match] = $data;
            }
        });
    }

    private function transformOsMetrics(Collect $data): void
    {
        $this->osMetrics = $data
            ->map(fn ($value, $key) => DataTransformer::transform($key, $value))
            ->values();
    }

    private function vmlistFormatting($list)
    {
        if (! is_array($list)) {
            return $list;
        }

        return collect($list)->map(function ($item) {
            if (array_key_exists('assigned_memory_B', $item)) {
                Arr::set($item, 'assigned_memory', Arr::pull($item, 'assigned_memory_B'));
            }

            return collect($item)->sortKeys();
        })->toArray();
    }

    public function addSnmpChecks(Collect $collection): void
    {
        if ($collection->isEmpty()) {
            $this->snmpChecks = collect();

            return;
        }

        $functionLookup = [
            'basedata'  => $this->formatSnmpBasedataMeasurements(),
            'bandwidth' => $this->formatSnmpBandwidthMeasurements(),
        ];

        $this->snmpChecks = $collection->map(function (CheckResult $checkResult) use ($functionLookup) {
            return [
                'dataUpdatedAt' => DateTransformer::transform($checkResult->data_updated_at),
                'success'       => $checkResult->success,
                'message'       => $checkResult->data['message'],
                'preset'        => $checkResult->data['check']['preset'],
                'measurements'  => collect($checkResult->data['measurements'])
                    ->reject(fn ($_, $key) => Str::is('snmpCheck.*.success', $key))
                    ->pipe(function ($collection) use ($checkResult, $functionLookup) {
                        return $functionLookup[$checkResult->data['check']['preset']]($collection);
                    })
                    ->all(),
                'frontmanId'    => $checkResult->frontman_id,
            ];
        });
    }

    private function formatSnmpBasedataMeasurements(): callable
    {
        return function (Collect $collection) {
            return $collection->map(
                fn ($value, $key) => DataTransformer::customCheckMeasurement($key, $value)
            )->values();
        };
    }

    private function formatSnmpBandwidthMeasurements(): callable
    {
        return function (Collect $collection) {
            return $collection->flatMap(function ($value, $key) {

                // TODO - this will send non-array values to frontend and will be used
                // to send success states like 'snmpCheck.bandwidth.success'
                // if (! is_array($value)) {
                //     return DataTransformer::customCheckMeasurement($key, $value);
                // }

                $interfaceName = "Interface {$key} [{$value['ifName.'.$key]}]";
                $measurements = [];
                foreach ($value as $bandwidthKey => $bandwidthValue) {
                    if (in_array($bandwidthKey, ['ifIndex.'.$bandwidthValue])) {
                        // Skip some keys which are quite useless for the customer and the frontend
                        continue;
                    }
                    if (Str::contains($bandwidthKey, 'ifAlias') && $bandwidthValue == '') {
                        continue;
                    }
                    if (Str::contains($bandwidthKey, 'ifSpeed_mbps') && $bandwidthValue === 0) {
                        continue;
                    }

                    $measurements[$interfaceName][] = DataTransformer::snmpCheckMeasurement(
                        $bandwidthKey,
                        $bandwidthValue
                    );
                }

                return $measurements;
            });
        };
    }

    public function addCustomChecks(Collect $collection): void
    {
        if ($collection->isEmpty()) {
            $this->customChecks = collect();

            return;
        }

        $collection->load('check');

        $this->customChecks = $collection->map(function (CheckResult $checkResult) {
            $customCheck = $checkResult->check;

            return [
                'dataUpdatedAt'   => DateTransformer::transform($checkResult->data_updated_at),
                'check'           => $customCheck->name,
                'lastInfluxError' => $customCheck->last_influx_error,
                'userAgent'       => $checkResult->user_agent,
                'measurements'    => collect($checkResult->data['measurements'])
                    ->map(function ($value, $key) {
                        return DataTransformer::customCheckMeasurement($key, $value);
                    })
                    ->values()
                    ->all(),
                'frontmanId'      => $checkResult->frontman_id,
            ];
        });
    }

    public function addWebChecks(Collect $collection): void
    {
        if ($collection->isEmpty()) {
            $this->webChecks = collect();

            return;
        }

        $this->webChecks = $collection->map(function (CheckResult $checkResult) {
            return [
                'dataUpdatedAt' => DateTransformer::transform($checkResult->data_updated_at),
                'check'         => $checkResult->data['check'],
                'measurements'  => collect($checkResult->data['measurements'])
                    ->map(fn ($value, $key) => DataTransformer::transform($key, $value))
                    ->values()
                    ->all(),
                'frontmanId'    => $checkResult->frontman_id,
            ];
        });
    }

    public function addServiceChecks(Collect $collection): void
    {
        if ($collection->isEmpty()) {
            $this->serviceChecks = collect();

            return;
        }

        $this->serviceChecks = $collection->map(function (CheckResult $checkResult) {
            return [
                'dataUpdatedAt' => DateTransformer::transform($checkResult->data_updated_at),
                'check'         => $checkResult->data['check'],
                'measurements'  => collect($checkResult->data['measurements'])
                    ->map(fn ($value, $key) => DataTransformer::transform($key, $value))
                    ->values()
                    ->all(),
                'frontmanId'    => $checkResult->frontman_id,
            ];
        });
    }
}
