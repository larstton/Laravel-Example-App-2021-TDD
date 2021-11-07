<?php

namespace App\Actions\Host\Guard;

use App\Data\Host\HostSnmpData;
use App\Exceptions\HostException;

class SnmpGuard
{
    public function __invoke(HostSnmpData $snmpData): void
    {
        if (! $snmpData->hasData()) {
            return;
        }

        if ($snmpData->isV2()) {
            throw_unless(
                filled($snmpData->community),
                HostException::invalidSnmpSettings('SNMP community is required with v2.')
            );

            return;
        }

        // v3 assertions...
        throw_unless(
            filled($snmpData->securityLevel),
            HostException::invalidSnmpSettings('SNMP security level is required with v3.')
        );

        switch ($snmpData->securityLevel) {
            case 'authNoPriv':
                $authValuesAreFilled = collect([
                    'username',
                    'authenticationPassword',
                    'authenticationProtocol',
                ])->every(fn ($property) => filled($snmpData->$property));

                throw_unless($authValuesAreFilled,
                    HostException::invalidSnmpSettings(
                        'Auth details are required for SNMP authNoPriv security level.'
                    )
                );
                break;

            case 'authPriv':
                $authValuesAreFilled = collect([
                    'privacyProtocol',
                    'authenticationProtocol',
                    'username',
                    'authenticationPassword',
                    'privacyPassword',
                ])->every(fn ($property) => filled($snmpData->$property));

                throw_unless($authValuesAreFilled,
                    HostException::invalidSnmpSettings(
                        'All v3 details are required for SNMP authPriv security level.'
                    )
                );
                break;
        }
    }
}
