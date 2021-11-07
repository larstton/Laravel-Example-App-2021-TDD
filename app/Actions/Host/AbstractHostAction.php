<?php

namespace App\Actions\Host;

use App\Actions\Host\Guard\BannedGuard;
use App\Actions\Host\Guard\CompatibilityGuard;
use App\Actions\Host\Guard\ConnectGuard;
use App\Actions\Host\Guard\FrontmanGuard;
use App\Actions\Host\Guard\HostUniquenessGuard;
use App\Actions\Host\Guard\PlanLevelGuard;
use App\Actions\Host\Guard\SnmpGuard;
use App\Data\Host\HostData;
use App\Models\Concerns\AuthedEntity;
use App\Models\Host;
use App\Models\Team;
use App\Support\Rule\RuleFactory;

class AbstractHostAction
{
    protected HostData $data;
    protected ?Host $host = null;
    protected AuthedEntity $authedEntity;
    protected Team $team;

    protected function fillSnmpData(Host $host): Host
    {
        $snmpData = $this->data->snmpData;
        $host->fill([
            'snmp_protocol'                => $snmpData->protocol,
            'snmp_timeout'                 => $snmpData->timeout ?? 5,
            'snmp_port'                    => $snmpData->port ?? 161,
            'snmp_community'               => $snmpData->community,
            'snmp_security_level'          => $snmpData->securityLevel,
            'snmp_privacy_protocol'        => $snmpData->privacyProtocol,
            'snmp_authentication_protocol' => $snmpData->authenticationProtocol,
            'snmp_username'                => $snmpData->username,
            'snmp_authentication_password' => $snmpData->authenticationPassword,
            'snmp_privacy_password'        => $snmpData->privacyPassword,
        ]);

        return $host;
    }

    protected function removeSnmpData(Host $host): Host
    {
        $host->fill([
            'snmp_protocol'                => null,
            'snmp_timeout'                 => null,
            'snmp_port'                    => null,
            'snmp_community'               => null,
            'snmp_security_level'          => null,
            'snmp_privacy_protocol'        => null,
            'snmp_authentication_protocol' => null,
            'snmp_username'                => null,
            'snmp_authentication_password' => null,
            'snmp_privacy_password'        => null,
        ]);

        return $host;
    }

    protected function guard()
    {
        resolve(HostUniquenessGuard::class)($this->data, $this->host);
        resolve(PlanLevelGuard::class)($this->authedEntity, $this->team);
        resolve(ConnectGuard::class)($this->data);
        resolve(BannedGuard::class)($this->data);
        resolve(FrontmanGuard::class)($this->data, $this->team);
        resolve(SnmpGuard::class)($this->data->snmpData);
        resolve(CompatibilityGuard::class)($this->data, $this->host);
    }

    protected function createAgentRules(): void
    {
        RuleFactory::makeDiskFullAlertRule($this->authedEntity)->saveIfNew();

        if (Host::withAgent()->count() === 1) {
            RuleFactory::makeLowMemoryWarningRule($this->authedEntity)->saveIfNew();
            RuleFactory::makeCPUHighLoadWarningRule($this->authedEntity)->saveIfNew();
            RuleFactory::makeModuleAlertRule($this->authedEntity)->saveIfNew();
            RuleFactory::makeModuleWarningRule($this->authedEntity)->saveIfNew();
        }
    }

    protected function removeAgentRules(): void
    {
        RuleFactory::makeDiskFullAlertRule($this->authedEntity)->deleteIfExists();
        RuleFactory::makeLowMemoryWarningRule($this->authedEntity)->deleteIfExists();
        RuleFactory::makeCPUHighLoadWarningRule($this->authedEntity)->deleteIfExists();
        RuleFactory::makeModuleAlertRule($this->authedEntity)->deleteIfExists();
        RuleFactory::makeModuleWarningRule($this->authedEntity)->deleteIfExists();
    }
}
