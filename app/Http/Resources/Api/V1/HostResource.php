<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Host;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Host
 */
class HostResource extends JsonResource
{
    public static $wrap = 'host';

    public function toArray($request)
    {
        return [
            'uuid'             => $this->id,
            'teamUuid'         => $this->team_id,
            'name'             => $this->name,
            'connect'          => $this->connect,
            'description'      => $this->description,
            'tags'             => $this->tags->pluck('name'),
            'active'           => $this->active,
            'cagent'           => $this->cagent,
            'cagentLastUpdate' => optional($this->cagent_last_updated_at)->timestamp ?? 0,
            'metrics'          => $this->cagent_metrics,
            'dashboard'        => (int) $this->dashboard,
            'muted'            => (int) $this->muted,
            'state'            => $this->state,
            'createTimestamp'  => $this->created_at->timestamp,
            'createdByUuid'    => $this->user_id,
            'frontman'         => $this->when(! is_null($this->frontman), function () {
                return [
                    'uuid'                   => $this->frontman->id,
                    'location'               => $this->frontman->location,
                    'lastHeartbeatTimestamp' => optional($this->frontman->last_heartbeat_at)->timestamp,
                    'type'                   => $this->frontman->isPublic() ? 'public' : 'private',
                ];
            }, []),
            $this->mergeWhen($this->snmp_protocol, [
                'snmpProtocol'               => $this->snmp_protocol,
                'snmpPort'                   => $this->snmp_port,
                'snmpCommunity'              => $this->snmp_community,
                'snmpTimeout'                => $this->snmp_timeout,
                'snmpPrivacyProtocol'        => $this->snmp_privacy_protocol,
                'snmpSecurityLevel'          => $this->snmp_security_level,
                'snmpAuthenticationProtocol' => $this->snmp_authentication_protocol,
                'snmpUsername'               => $this->snmp_username,
                'snmpAuthenticationPassword' => $this->snmp_authentication_password,
                'snmpPrivacyPassword'        => $this->snmp_privacy_password,
            ]),
            'hub_password'     => $this->password,
            'hub_url'          => config('app.hub_url'),
        ];
    }
}
