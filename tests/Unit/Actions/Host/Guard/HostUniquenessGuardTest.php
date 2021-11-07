<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\HostUniquenessGuard;
use App\Exceptions\HostException;
use App\Models\Host;
use App\Support\NotifierService;
use App\Support\Tenancy\Facades\TenantManager;
use Database\Factories\HostDataFactory;
use Mockery\MockInterface;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class HostUniquenessGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_guard_against_duplicate_host_names_for_team()
    {
        Host::factory()->create([
            'name' => 'name1',
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name1',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the name 'name1' already exists.");

        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function will_permit_non_duplicate_names()
    {
        Host::factory()->create([
            'name' => 'name1',
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name2',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function duplicate_name_checks_are_team_scoped()
    {
        TenantManager::enableTenancyChecks();

        $team = $this->createTeam();
        Host::factory()->create([
            'name'    => 'name1',
            'team_id' => $team->id,
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name1',
        ]);

        $this->expectNotToPerformAssertions();

        $this->createTeam();
        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function will_ignore_current_host_when_passed_when_checking_for_duplicate_name()
    {
        TenantManager::enableTenancyChecks();

        $team = $this->createTeam();
        $host = Host::factory()->create([
            'name'    => 'name1',
            'team_id' => $team->id,
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name1',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(HostUniquenessGuard::class)($hostData, $host);
    }

    /** @test */
    public function will_guard_against_duplicate_connect_for_team()
    {
        Host::factory()->create([
            'connect' => '8.8.8.8',
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.8',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the connect (FQDN/IP) '8.8.8.8' already exists.");

        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function wont_check_for_duplicate_connect_if_no_connect_provided()
    {
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function will_permit_non_duplicate_connects()
    {
        Host::factory()->create([
            'connect' => '8.8.8.1',
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.2',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function duplicate_connect_checks_are_team_scoped()
    {
        TenantManager::enableTenancyChecks();

        $team = $this->createTeam();
        Host::factory()->create([
            'connect' => '8.8.8.1',
            'team_id' => $team->id,
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.1',
        ]);

        $this->expectNotToPerformAssertions();

        $this->createTeam();
        resolve(HostUniquenessGuard::class)($hostData, null);
    }

    /** @test */
    public function will_ignore_current_host_when_passed_when_checking_for_duplicate_connect()
    {
        TenantManager::enableTenancyChecks();

        $team = $this->createTeam();
        $host = Host::factory()->create([
            'connect' => '8.8.8.1',
            'team_id' => $team->id,
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.1',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(HostUniquenessGuard::class)($hostData, $host);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTeam();
        $this->mock(NotifierService::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();
        });
    }
}
