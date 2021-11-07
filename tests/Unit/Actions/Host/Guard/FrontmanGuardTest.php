<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\FrontmanGuard;
use App\Exceptions\TeamException;
use App\Models\Frontman;
use App\Models\Team;
use Database\Factories\HostDataFactory;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class FrontmanGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_guard_against_team_missing_a_default_frontman_when_one_not_supplied()
    {
        $team = $this->createTeam([
            'default_frontman_id' => Frontman::DEFAULT_FRONTMAN_UUID,
        ]);
        $hostData = HostDataFactory::make([
            'frontman' => null,
        ]);

        $this->expectException(TeamException::class);
        $this->expectErrorMessage('No default frontman set for the team.');

        resolve(FrontmanGuard::class)($hostData, $team);
    }

    /** @test */
    public function will_guard_against_supplying_an_invalid_frontman_for_team()
    {
        $team = $this->createTeam();
        $hostData = HostDataFactory::make([
            'frontman' => $frontman = Frontman::factory()->create([
                'team_id' => Team::factory(),
            ]),
        ]);

        $this->expectException(TeamException::class);
        $this->expectErrorMessage("The given frontman UUID '{$frontman->id}' does not exist or does not belong to your team.");

        resolve(FrontmanGuard::class)($hostData, $team);
    }

    /** @test */
    public function will_pass_if_no_frontman_supplied_and_team_has_default_preset()
    {
        $team = $this->createTeam();
        $hostData = HostDataFactory::make([
            'frontman' => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(FrontmanGuard::class)($hostData, $team);
    }

    /** @test */
    public function will_pass_if_frontman_supplied_and_is_owned_by_team()
    {
        $team = $this->createTeam();
        $hostData = HostDataFactory::make([
            'frontman' => $frontman = Frontman::factory()->create([
                'team_id' => $team->id,
            ]),
        ]);

        $this->expectNotToPerformAssertions();

        resolve(FrontmanGuard::class)($hostData, $team);
    }

    /** @test */
    public function will_pass_if_frontman_supplied_and_is_private_frontman()
    {
        $team = $this->createTeam();
        $hostData = HostDataFactory::make([
            'frontman' => Frontman::where('location', 'EU-WEST-Netherlands')->first(),
        ]);

        $this->expectNotToPerformAssertions();

        resolve(FrontmanGuard::class)($hostData, $team);
    }
}
