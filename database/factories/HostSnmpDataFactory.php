<?php

namespace Database\Factories;

use App\Data\Host\HostSnmpData;
use Illuminate\Foundation\Testing\WithFaker;

class HostSnmpDataFactory
{
    use WithFaker;

    public static function make(array $params = []): HostSnmpData
    {
        return new HostSnmpData(array_merge([
            'protocol'               => 'v3',
            'community'              => 'snmp-community',
            'port'                   => 100,
            'timeout'                => 2,
            'privacyProtocol'        => 'aes',
            'securityLevel'          => 'authPriv',
            'authenticationProtocol' => 'sha',
            'username'               => 'snmp-username',
            'authenticationPassword' => 'snmp-auth-password',
            'privacyPassword'        => 'snmp-privacy-password',
        ], $params));
    }
}
