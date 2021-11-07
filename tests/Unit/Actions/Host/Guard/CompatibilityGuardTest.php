<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\CompatibilityGuard;
use App\Exceptions\HostException;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\WebCheck;
use Database\Factories\HostDataFactory;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class CompatibilityGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_not_guard_if_no_host_supplied()
    {
        $hostData = HostDataFactory::make();

        $this->expectNotToPerformAssertions();

        resolve(CompatibilityGuard::class)($hostData, null);
    }

    /** @test */
    public function will_not_guard_if_supplied_host_has_no_connect()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => null,
        ]);
        $hostData = HostDataFactory::make();

        $this->expectNotToPerformAssertions();

        resolve(CompatibilityGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_guard_against_changing_connect_when_host_has_service_checks()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);
        ServiceCheck::factory()->create([
            'host_id' => $host->id,
        ]);
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('You cannot remove the FQDN/IP from a host with service and/or web checks.');

        resolve(CompatibilityGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_guard_against_changing_connect_when_host_has_web_checks()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);
        WebCheck::factory()->create([
            'host_id' => $host->id,
        ]);
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('You cannot remove the FQDN/IP from a host with service and/or web checks.');

        resolve(CompatibilityGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_guard_against_changing_connect_when_host_has_service_and_web_checks()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);
        ServiceCheck::factory()->create([
            'host_id' => $host->id,
        ]);
        WebCheck::factory()->create([
            'host_id' => $host->id,
        ]);
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage('You cannot remove the FQDN/IP from a host with service and/or web checks.');

        resolve(CompatibilityGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_pass_when_not_removing_connect()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);
        ServiceCheck::factory()->create([
            'host_id' => $host->id,
        ]);
        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.1',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(CompatibilityGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_pass_when_host_has_no_checks()
    {
        $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(CompatibilityGuard::class)($hostData, $host);
    }
}
