<?php

namespace Actions\Team;

use App\Actions\Host\ActivateAllTeamHostsAction;
use App\Actions\Host\DeactivateAllTeamHostsAction;
use App\Actions\Host\UpdateCheckIntervalsForHostChecksAction;
use App\Actions\Team\CreatePaidHostHistoryOnTeamPlanUpgradeAction;
use App\Actions\Team\HandleMovingToFrozenPlanAction;
use App\Actions\Team\NotifyAdminsTeamPlanDowngradedAction;
use App\Actions\Team\NotifyAdminsTeamPlanUpgradedAction;
use App\Actions\Team\SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction;
use App\Actions\Team\UpdateTeamPlanAction;
use App\Enums\TeamPlan;
use App\Events\Team\TeamPlanDowngraded;
use App\Events\Team\TeamPlanUpgraded;
use App\Models\Team;
use Database\Factories\TeamManagementUpdateDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateTeamPlanActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_team_plan()
    {
        $team = $this->createTeam([
            'plan'                 => TeamPlan::Trial(),
            'currency'             => 'GBP',
            'max_hosts'            => 50,
            'max_recipients'       => 50,
            'data_retention'       => 50,
            'max_members'          => 50,
            'max_frontmen'         => 50,
            'min_check_interval'   => 50,
            'trial_ends_at'        => now()->addDay(),
            'plan_last_changed_at' => null,
            'previous_plan'        => null,
            'upgraded_at'          => null,
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan'             => TeamPlan::Payg(),
            'maxHosts'         => 100,
            'maxRecipients'    => 100,
            'dataRetention'    => 100,
            'maxMembers'       => 100,
            'maxFrontmen'      => 100,
            'minCheckInterval' => 100,
            'currency'         => 'EUR',
        ]);

        Carbon::setTestNow($now = now());

        $team = resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertTrue($team->plan->is(TeamPlan::Payg()));
        $this->assertDateTimesMatch($now, $team->upgraded_at);
        $this->assertEquals(100, $team->max_hosts);
        $this->assertEquals(100, $team->max_recipients);
        $this->assertEquals(100, $team->data_retention);
        $this->assertEquals(100, $team->max_members);
        $this->assertEquals(100, $team->max_frontmen);
        $this->assertEquals(100, $team->min_check_interval);
        $this->assertNull($team->trial_ends_at);
        $this->assertDateTimesMatch($now, $team->plan_last_changed_at);
        $this->assertTrue($team->previous_plan->is(TeamPlan::Trial()));
        $this->assertEquals('EUR', $team->currency);
    }

    /** @test */
    public function will_activate_hosts_if_unfreezing_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Frozen(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Payg(),
        ]);

        $spy = $this->spy(ActivateAllTeamHostsAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_create_paid_host_history_when_upgrading_to_payg_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Payg(),
        ]);

        $spy = $this->spy(CreatePaidHostHistoryOnTeamPlanUpgradeAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_notify_team_admins_when_upgrading_to_payg_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Payg(),
        ]);

        $spy = $this->spy(NotifyAdminsTeamPlanUpgradedAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_dispatch_event_when_upgrading_to_payg_plan()
    {
        Event::fake([
            TeamPlanUpgraded::class,
        ]);

        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Payg(),
        ]);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        Event::assertDispatched(TeamPlanUpgraded::class);
    }

    /** @test */
    public function will_soft_delete_host_history_when_downgrading_from_payg_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Payg(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Frozen(),
        ]);

        $spy = $this->spy(SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_notify_team_admins_when_downgrading_from_payg_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Payg(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Frozen(),
        ]);

        $spy = $this->spy(NotifyAdminsTeamPlanDowngradedAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_dispatch_event_when_downgrading_from_payg_plan()
    {
        Event::fake([
            TeamPlanDowngraded::class,
        ]);

        $team = $this->createTeam([
            'plan' => TeamPlan::Payg(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Frozen(),
        ]);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        Event::assertDispatched(TeamPlanDowngraded::class);
    }

    /** @test */
    public function will_deactivate_hosts_when_exceeded_max()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);
        $this->createHost($team);
        $this->createHost($team);

        $data = TeamManagementUpdateDataFactory::make([
            'plan'     => TeamPlan::Payg(),
            'maxHosts' => 1,
        ]);

        $spy = $this->spy(DeactivateAllTeamHostsAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_update_check_intervals()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Payg(),
        ]);

        $spy = $this->spy(UpdateCheckIntervalsForHostChecksAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function will_handle_moving_to_frozen_plan()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Trial(),
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Frozen(),
        ]);

        $spy = $this->spy(HandleMovingToFrozenPlanAction::class);

        resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $spy->shouldHaveReceived('execute')->once()->with($team);
    }

    /** @test */
    public function wont_change_previous_plan_fields_if_plan_not_changed()
    {
        $team = $this->createTeam([
            'plan'                 => TeamPlan::Trial(),
            'plan_last_changed_at' => null,
            'previous_plan'        => null,
        ]);

        $data = TeamManagementUpdateDataFactory::make([
            'plan' => TeamPlan::Trial(),
        ]);

        $team = resolve(UpdateTeamPlanAction::class)->execute($team, $data);

        $this->assertNull($team->plan_last_changed_at);
        $this->assertNull($team->previous_plan);
    }
}
