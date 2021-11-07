<?php

namespace Tests\Unit\Policies;

use App\Enums\TeamPlan;
use App\Models\Host;
use App\Models\Team;
use App\Models\User;
use App\Policies\HostPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class HostPolicyTest extends TestCase
{
    use WithoutEvents, WithoutTenancyChecks;

    /** @test */
    public function is_allowed_to_view_index()
    {
        $this->assertTrue((new HostPolicy)->viewAny(User::factory()->create()));
    }

    /** @test */
    public function is_allowed_to_view_hosts_owned_by_team()
    {
        /** @var User $user */
        $this->assertTrue((new HostPolicy)->view(
            $user = User::factory()->create(),
            Host::factory()->create(['team_id' => $user->team->id])
        ));
    }

    /** @test */
    public function is_not_allowed_to_view_hosts_owned_by_another_team()
    {
        $this->assertFalse((new HostPolicy)->update(
            User::factory()->create(),
            Host::factory()->create()
        ));
    }

    /** @test */
    public function is_allowed_to_create_host()
    {
        $this->assertTrue((new HostPolicy)->create(
            User::factory()->create(),
        ));
    }

    /** @test */
    public function is_not_allowed_to_create_host_if_team_plan_is_frozen()
    {
        $response = (new HostPolicy)->create(
            User::factory()->create([
                'team_id' => Team::factory()->create([
                    'plan' => TeamPlan::Frozen(),
                ]),
            ]),
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->denied());
        $this->assertEquals('Your trial has ended. Please upgrade your plan.', $response->message());
    }

    /** @test */
    public function is_not_allowed_to_create_host_if_team_has_exceeded_max_hosts()
    {
        $team = $this->createTeam([
            'max_hosts' => 1,
        ]);
        Host::factory()->create([
            'team_id' => $team->id,
        ]);

        $response = (new HostPolicy)->create(
            User::factory()->create([
                'team_id' => $team->id,
            ]),
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->denied());
        $this->assertEquals('Maximum of allowed hosts reached.', $response->message());
    }

    /** @test */
    public function is_allowed_to_update_hosts_owned_by_team()
    {
        $team = $this->createTeam();
        $host = Host::factory()->create([
            'team_id' => $team->id,
        ]);

        $this->assertTrue((new HostPolicy)->update(
            User::factory()->create([
                'team_id' => $team->id,
            ]),
            $host
        ));
    }

    /** @test */
    public function is_not_allowed_to_update_hosts_owned_by_another_team()
    {
        $team = $this->createTeam();
        $host = Host::factory()->create([
            'team_id' => Team::factory(),
        ]);

        $this->assertFalse((new HostPolicy)->update(
            User::factory()->create([
                'team_id' => $team->id,
            ]),
            $host
        ));
    }

    /** @test */
    public function is_not_allowed_to_update_host_if_team_plan_is_frozen()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Frozen(),
        ]);
        $host = Host::factory()->create([
            'team_id' => $team->id,
        ]);
        $response = (new HostPolicy)->update(
            User::factory()->create([
                'team_id' => $team->id,
            ]),
            $host
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->denied());
        $this->assertEquals('Your trial has ended. Please upgrade your plan.', $response->message());
    }

    /** @test */
    public function is_allowed_to_delete_hosts_owned_by_team()
    {
        /** @var User $user */
        $this->assertTrue((new HostPolicy)->delete(
            $user = User::factory()->create(),
            Host::factory()->create(['team_id' => $user->refresh()->team->id])
        ));
    }

    /** @test */
    public function is_not_allowed_to_delete_hosts_owned_by_another_team()
    {
        $this->assertFalse((new HostPolicy)->delete(
            User::factory()->create(),
            Host::factory()->create()
        ));
    }
}
