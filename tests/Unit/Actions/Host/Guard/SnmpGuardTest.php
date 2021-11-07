<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\SnmpGuard;
use App\Exceptions\HostException;
use Database\Factories\HostSnmpDataFactory;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class SnmpGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_not_guard_if_no_snmp_data_available()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'               => null,
            'community'              => null,
            'port'                   => null,
            'timeout'                => null,
            'privacyProtocol'        => null,
            'securityLevel'          => null,
            'authenticationProtocol' => null,
            'username'               => null,
            'authenticationPassword' => null,
            'privacyPassword'        => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_community_when_v2()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'  => 'v2',
            'community' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('SNMP community is required with v2.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_security_level_when_v2()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'      => 'v3',
            'securityLevel' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('SNMP security level is required with v3.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_username_when_v3_and_auth_no_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'      => 'v3',
            'securityLevel' => 'authNoPriv',
            'username'      => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('Auth details are required for SNMP authNoPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_authentication_password_when_v3_and_auth_no_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'               => 'v3',
            'securityLevel'          => 'authNoPriv',
            'authenticationPassword' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('Auth details are required for SNMP authNoPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_authentication_protocol_when_v3_and_auth_no_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'               => 'v3',
            'securityLevel'          => 'authNoPriv',
            'authenticationProtocol' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('Auth details are required for SNMP authNoPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_privacy_protocol_when_v3_and_auth_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'        => 'v3',
            'securityLevel'   => 'authPriv',
            'privacyProtocol' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('All v3 details are required for SNMP authPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_authentication_protocol_when_v3_and_auth_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'               => 'v3',
            'securityLevel'          => 'authPriv',
            'authenticationProtocol' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('All v3 details are required for SNMP authPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_username_when_v3_and_auth_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'      => 'v3',
            'securityLevel' => 'authPriv',
            'username'      => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('All v3 details are required for SNMP authPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_authentication_password_when_v3_and_auth_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'               => 'v3',
            'securityLevel'          => 'authPriv',
            'authenticationPassword' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('All v3 details are required for SNMP authPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_guard_against_missing_privacy_password_when_v3_and_auth_priv_security_level()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'        => 'v3',
            'securityLevel'   => 'authPriv',
            'privacyPassword' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('All v3 details are required for SNMP authPriv security level.');

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_pass_v2_with_community()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'  => 'v2',
            'community' => 'community',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_pass_v3_and_auth_no_priv_with_required_params()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'      => 'v3',
            'securityLevel' => 'authNoPriv',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(SnmpGuard::class)($snmpData);
    }

    /** @test */
    public function will_pass_v3_and_auth_priv_with_required_params()
    {
        $snmpData = HostSnmpDataFactory::make([
            'protocol'      => 'v3',
            'securityLevel' => 'authPriv',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(SnmpGuard::class)($snmpData);
    }
}
