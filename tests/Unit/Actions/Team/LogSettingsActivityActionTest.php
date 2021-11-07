<?php

namespace Actions\Team;

use App\Actions\Team\LogSettingsActivityAction;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class LogSettingsActivityActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_log_activity_when_disabling_subunit_management()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        team_settings($team)->set([
            'subUnitManagementEnabled' => true,
        ]);

        resolve(LogSettingsActivityAction::class)->execute(
            $user,
            ['subUnitManagementEnabled' => false],
            ['subUnitManagementEnabled' => true],
            [],
            $team->id
        );

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => $user->id,
            'causer_type'  => User::class,
            'subject_id'   => $team->id,
            'subject_type' => Team::class,
            'description'  => 'Sub-unit management disabled.',
        ]);
    }

    /** @test */
    public function will_log_activity_when_enabling_subunit_management()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        team_settings($team)->set([
            'subUnitManagementEnabled' => false,
        ]);

        resolve(LogSettingsActivityAction::class)->execute(
            $user,
            ['subUnitManagementEnabled' => true],
            ['subUnitManagementEnabled' => false],
            [],
            $team->id
        );

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => $user->id,
            'causer_type'  => User::class,
            'subject_id'   => $team->id,
            'subject_type' => Team::class,
            'description'  => 'Sub-unit management enabled.',
        ]);
    }

    /** @test */
    public function wont_log_activity_for_subunit_management_when_not_changing_it()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        team_settings($team)->set([
            'subUnitManagementEnabled' => false,
        ]);

        resolve(LogSettingsActivityAction::class)->execute(
            $user,
            ['subUnitManagementEnabled' => false],
            ['subUnitManagementEnabled' => false],
            [],
            $team->id
        );

        $this->assertDatabaseMissing('activity_log', [
            'team_id'     => $team->id,
            'description' => 'Sub-unit management disabled.',
        ]);
        $this->assertDatabaseMissing('activity_log', [
            'team_id'     => $team->id,
            'description' => 'Sub-unit management enabled.',
        ]);
    }

    /** @test */
    public function wont_log_activity_for_subunit_management_if_not_in_changing_settings()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        team_settings($team)->set([
            'subUnitManagementEnabled' => true,
        ]);

        resolve(LogSettingsActivityAction::class)->execute(
            $user,
            ['xxx' => false],
            ['subUnitManagementEnabled' => true],
            [],
            $team->id
        );

        $this->assertDatabaseMissing('activity_log', [
            'team_id'      => $team->id,
            'description'  => 'Sub-unit management disabled.',
        ]);
        $this->assertDatabaseMissing('activity_log', [
            'team_id'      => $team->id,
            'description'  => 'Sub-unit management enabled.',
        ]);
    }
}
