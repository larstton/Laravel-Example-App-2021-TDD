<?php

namespace App\Support\Influx;

use App\Enums\CheckType;
use App\Models\CheckResult;
use App\Models\Host;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InfluxKeyService
{
    protected $frontmanGroups = [
        'ifOut_Bps.*',
        'ifIn_Bps.*',
        'ifOutUtilization_percent.*',
        'ifInUtilization_percent.*',
        'ifSpeed_mbps.*',
        'ifInOctets.*',
        'ifOutOctets.*',
    ];
    protected $cagentGroups = [
        'cpu.*',
        'fs.*',
        'mem.*',
        'net.*',
    ];

    public function getHostKeys(Host $host): Collection
    {
        [$customChecks, $nonCustomChecks] = CheckResult::whereHostId($host->id)->get()->partition(
            fn (CheckResult $checkResult) => $checkResult->check_type === CheckType::CustomCheck
        );

        $dictionary = [
            'customChecksResults' => [],
            'checkResults'        => [],
        ];

        $lookup = [
            CheckType::ServiceCheck => fn ($checkResult) => $this->getCheckMeasurements($checkResult),
            CheckType::WebCheck     => fn ($checkResult) => $this->getCheckMeasurements($checkResult),
            CheckType::Agent        => fn ($checkResult) => $this->getCagentMeasurements($checkResult),
            CheckType::SnmpCheck    => fn ($checkResult) => $this->getSNMPCheckMeasurements($checkResult),
        ];

        $dictionary['customChecksResults'] = $customChecks->flatMap(
            fn (CheckResult $checkResult) => $this->getCustomCheckMeasurements($checkResult)
        )->filter()->all();

        $dictionary['checkResults'] = $nonCustomChecks->flatMap(
            fn (CheckResult $checkResult) => $lookup[$checkResult->check_type]($checkResult)
        )->filter()->all();

        return collect($dictionary);
    }

    protected function getCheckMeasurements(CheckResult $check): array
    {
        $data = $check->data;

        if (! isset($data['measurements'])) {
            return [];
        }

        $measurements = collect($data['measurements']);

        return $measurements->keys()->mapWithKeys(function ($key) {
            return [$key => $this->getFrontmanMeasurementName($key)];
        })->all();
    }

    protected function getFrontmanMeasurementName($keyName)
    {
        return $this->getKeyForGroup($this->frontmanGroups, $keyName);
    }

    private function getKeyForGroup($groupStorage, $keyName)
    {
        return collect($groupStorage)
            ->first(fn ($group) => Str::is($group, $keyName), $keyName);
    }

    protected function getCagentMeasurements($check): array
    {
        $data = $check->data;

        if (! isset($data['measurements'])) {
            return [];
        }

        $measurements = collect($data['measurements']);

        return $measurements->keys()->mapWithKeys(function ($key) {
            return [$key => $this->getCagentMeasurementName($key)];
        })->merge(collect($measurements->get('temperatures.list', []))->mapWithKeys(function ($item) {
            return [
                $item['sensor_name'].".temperature.temp"        => 'temperatures.list',
                $item['sensor_name'].".critical_threshold.temp" => 'temperatures.list',
            ];
        }))->all();
    }

    protected function getCagentMeasurementName($keyName)
    {
        return $this->getKeyForGroup($this->cagentGroups, $keyName);
    }

    protected function getSNMPCheckMeasurements(CheckResult $check): array
    {
        return collect(optional($check->data)['measurements'] ?? [])
            ->filter(fn ($measurements) => is_array($measurements))
            ->flatMap(fn ($measurements) => collect($measurements)
                ->mapWithKeys(fn ($_, $key) => [$key => $this->getFrontmanMeasurementName($key)])
                ->all())
            ->unique()
            ->all();
    }

    protected function getCustomCheckMeasurements(CheckResult $check): array
    {
        $data = $check->data;

        if (! isset($data['measurements'])) {
            return [];
        }

        $measurements = collect($data['measurements']);

        return $measurements->keys()->mapWithKeys(function ($key) use ($check) {
            return [$key => $check->check_id];
        })->all();
    }
}
