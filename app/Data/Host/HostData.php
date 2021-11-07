<?php

namespace App\Data\Host;

use App\Data\BaseData;
use App\Http\Api\V1\Requests\HostRequest as ApiV1HostRequest;
use App\Http\Requests\Host\HostRequest;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\SubUnit;
use Illuminate\Support\Str;

class HostData extends BaseData
{
    public string $name;
    public ?string $connect;
    public ?string $description;
    public bool $cagent;
    public bool $active;
    public bool $dashboard;
    public bool $muted;
    public ?Frontman $frontman;
    public ?SubUnit $subUnit;
    public ?array $tags = null;
    public HostSnmpData $snmpData;

    public static function fromRequest(HostRequest $request): self
    {
        return new self([
            'name'        => $request->name,
            'description' => $request->description,
            'connect'     => $request->connect,
            'cagent'      => (bool) $request->input('cagent', false),
            'dashboard'   => (bool) $request->input('dashboard', true),
            'muted'       => (bool) $request->input('muted', false),
            'active'      => (bool) $request->input('active', true),
            'frontman'    => Frontman::find($request->frontmanId),
            'subUnit'     => SubUnit::find($request->subUnitId),
            'tags'        => $request->tags ?? null,
            'snmpData'    => self::insertSnmpData(data_get($request->all(), 'snmp', [])),
        ]);
    }

    private static function insertSnmpData(array $snmpData): array
    {
        if (filled($snmpData)) {
            $snmpData['port'] = self::nullableIntCast(data_get($snmpData, 'port'));
            $snmpData['timeout'] = self::nullableIntCast(data_get($snmpData, 'timeout'));
        }

        return $snmpData;
    }

    public static function fromApiV1Request(ApiV1HostRequest $request, ?Host $host = null): self
    {
        // Patch request so need to merge Host into request to then pass to DTO
        // This isn't nice but its only for api v1. Will change with v2.

        $host = optional($host);

        $snmpData = collect($request)
            ->filter(fn ($value, $key) => Str::startsWith($key, 'snmp'))
            ->mapWithKeys(fn ($value, $key) => [
                (string) Str::of($key)->after('snmp')->camel() => $value,
            ]);

        if ($snmpData->isEmpty()) {
            $snmpData = collect($host->toArray())
                ->filter(fn ($value, $key) => Str::startsWith($key, 'snmp'))
                ->mapWithKeys(fn ($value, $key) => [
                    (string) Str::of($key)->after('snmp_')->camel() => $value,
                ])
                ->reject(fn ($value, $key) => Str::contains($key, [
                    'checkLastUpdatedAt', 'checksCount', 'lastCheckedAt',
                ]));
        }

        $frontman = Frontman::find($request->frontman ?? $host->frontman_id);
        $subUnit = SubUnit::find($request->customerUuid ?? $host->sub_unit_id);
        $tags = optional(optional($host->tags)->pluck('name'))->all();

        return new self([
            'name'        => $request->name ?? $host->name,
            'connect'     => $request->connect ?? $host->connect ?? null,
            'description' => $request->description ?? $host->description ?? null,
            'cagent'      => (bool) ($request->cagent ?? $host->cagent ?? false),
            'dashboard'   => (bool) ($request->dashboard ?? $host->dashboard ?? true),
            'active'      => (bool) ($request->active ?? $host->active ?? true),
            'muted'       => (bool) ($request->muted ?? $host->muted ?? false),
            'frontman'    => $frontman,
            'subUnit'     => $subUnit,
            'tags'        => $request->tags ?? $tags ?? [],
            'snmpData'    => self::insertSnmpData($snmpData->all()),
        ]);
    }
}
