<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\ConnectGuard;
use App\Exceptions\HostException;
use App\Models\Frontman;
use Database\Factories\HostDataFactory;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class ConnectGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_not_guard_if_no_connect_supplied()
    {
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(ConnectGuard::class)($hostData);
    }

    /** @test */
    public function will_guard_against_invalid_ip_address_when_frontman_supplied()
    {
        $this->createTeam();
        $hostData = HostDataFactory::make([
            'connect'  => '55555555.55555555.55555555.1',
            'frontman' => Frontman::factory()->create(),
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("'55555555.55555555.55555555.1' is not a valid FQDN/IP.");

        resolve(ConnectGuard::class)($hostData);
    }

    /** @test */
    public function will_guard_against_invalid_fqdn_when_frontman_supplied()
    {
        $this->createTeam();
        $hostData = HostDataFactory::make([
            'connect'  => 'invalid!@£-domain.com',
            'frontman' => Frontman::factory()->create(),
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("'invalid!@£-domain.com' is not a valid FQDN/IP.");

        resolve(ConnectGuard::class)($hostData);
    }

    /** @test */
    public function will_guard_against_invalid_ip_address_when_agent_is_true()
    {
        $this->createTeam();
        $hostData = HostDataFactory::make([
            'connect' => 'invalid!@£-domain.com',
            'cagent'  => true,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("'invalid!@£-domain.com' is not a valid FQDN/IP.");

        resolve(ConnectGuard::class)($hostData);
    }

    /** @test */
    public function will_guard_against_invalid_fqdn_when_agent_is_true()
    {
        $this->createTeam();
        $hostData = HostDataFactory::make([
            'connect' => 'invalid!@£-domain.com',
            'cagent'  => true,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("'invalid!@£-domain.com' is not a valid FQDN/IP.");

        resolve(ConnectGuard::class)($hostData);
    }
}
