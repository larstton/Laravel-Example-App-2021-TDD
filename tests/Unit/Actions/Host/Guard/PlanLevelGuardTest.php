<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\PlanLevelGuard;
use App\Enums\TeamPlan;
use App\Exceptions\TeamException;
use App\Models\Host;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class PlanLevelGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_guard_against_frozen_accounts_trying_to_create_hosts()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Frozen(),
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);

        $this->expectException(TeamException::class);
        $this->expectErrorMessage('Trial expired');

        resolve(PlanLevelGuard::class)($user, $team);
    }

    /** @test */
    public function will_pass_with_non_frozen_team_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Payg(),
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);

        $this->expectNotToPerformAssertions();

        resolve(PlanLevelGuard::class)($user, $team);
    }

    /** @test */
    public function will_not_check_if_authed_entity_is_api_token()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Frozen(),
        ]);
        $apiToken = $this->createApiToken([
            'team_id' => $team->id,
        ], false);

        $this->expectNotToPerformAssertions();

        resolve(PlanLevelGuard::class)($apiToken, $team);
    }

    /** @test */
    public function will_guard_against_max_hosts_being_exceeded()
    {
        $team = $this->createTeam([
            'max_hosts' => 1,
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);

        Host::factory()->create([
            'team_id' => $team->id,
        ]);

        $this->expectException(TeamException::class);
        $this->expectErrorMessage('Maximum hosts reached');

        resolve(PlanLevelGuard::class)($user, $team);
    }

    /** @test */
    public function will_pass_when_not_exceeding_max_hosts()
    {
        $team = $this->createTeam([
            'max_hosts' => 2,
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);

        Host::factory()->create([
            'team_id' => $team->id,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(PlanLevelGuard::class)($user, $team);
    }
}
