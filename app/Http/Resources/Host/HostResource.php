<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\Frontman\HostFrontmanResource;
use App\Http\Resources\JsonResource;
use App\Http\Resources\SubUnitResource;
use App\Http\Transformers\DateTransformer;
use App\Models\Host;
use Illuminate\Support\Str;

/**
 * @mixin Host
 */
class HostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'teamId'       => $this->team_id,
            'userId'       => $this->user_id,
            'subUnitId'    => $this->sub_unit_id,
            'subUnit'      => $this->whenLoaded('subUnit',
                fn () => SubUnitResource::make($this->subUnit)
            ),
            'name'         => $this->name,
            'description'  => $this->description,
            'connect'      => $this->connect,
            'active'       => $this->active->value,
            'cagent'       => $this->usesMonitoringAgent(),
            'dashboard'    => $this->dashboard,
            'muted'        => $this->muted,
            'alerting'     => ! $this->muted,
            'tags'         => $this->tags->pluck('name'),
            'extendedTags' => $this->when($this->tags->isNotEmpty(), $this->getExtendedTags()),
            'snmp'         => [
                'protocol'               => $this->snmp_protocol,
                'port'                   => $this->snmp_port,
                'community'              => $this->snmp_community,
                'timeout'                => $this->snmp_timeout,
                'privacyProtocol'        => $this->snmp_privacy_protocol,
                'securityLevel'          => $this->snmp_security_level,
                'authenticationProtocol' => $this->snmp_authentication_protocol,
                'username'               => $this->snmp_username,
                'authenticationPassword' => $this->snmp_authentication_password,
                'privacyPassword'        => $this->snmp_privacy_password,
            ],
            'summary'      => $this->summary->toArray(),
            'eventSummary' => $this->when(Str::contains(request('append', ''), 'events'),
                fn () => $this->event_summary->toArray()
            ),
            'frontmanId'   => $this->frontman_id,
            'frontman'     => $this->whenLoaded('frontman', function () {
                return HostFrontmanResource::make($this->frontman);
            }),
            'inventory'    => $this->inventory ?? [],
            'hwInventory'  => $this->when(! is_null($this->hw_inventory), $this->hw_inventory),
            'checkCounts'  => [
                'hasIcmpCheck'      => $this->when(
                    ! is_null($this->has_icmp_check),
                    (bool) $this->has_icmp_check
                ),
                'webCheckCount'     => $this->when(
                    ! is_null($this->web_checks_count),
                    $this->web_checks_count
                ),
                'serviceCheckCount' => $this->when(
                    ! is_null($this->service_checks_count),
                    $this->service_checks_count
                ),
                'snmpCheckCount'    => $this->when(
                    ! is_null($this->snmp_checks_count),
                    $this->snmp_checks_count
                ),
                'customCheckCount'  => $this->when(
                    ! is_null($this->custom_checks_count),
                    $this->custom_checks_count
                ),
                'checkCountTotal'   => $this->when(
                    ! is_null($this->check_count_total),
                    $this->check_count_total
                ),
            ],
            'dates'        => [
                'lastCheckedAt'       => DateTransformer::transform($this->getLastCheckTime()),
                'snmpLastUpdatedAt'   => DateTransformer::transform($this->snmp_last_checked_at),
                'cagentLastUpdatedAt' => DateTransformer::transform($this->cagent_last_updated_at),

                'serviceCheckLastUpdatedAt' => DateTransformer::transform($this->service_check_last_updated_at),
                'webCheckLastUpdatedAt'     => DateTransformer::transform($this->web_check_last_updated_at),
                'snmpCheckLastUpdatedAt'    => DateTransformer::transform($this->snmp_check_last_updated_at),
                'customCheckLastUpdatedAt'  => DateTransformer::transform($this->custom_check_last_updated_at),

                'updatedAt'           => DateTransformer::transform($this->updated_at),
                'createdAt'           => DateTransformer::transform($this->created_at),
            ],
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'availableServiceChecks' => Host::availableServiceChecks(),
            ],
        ];
    }
}
