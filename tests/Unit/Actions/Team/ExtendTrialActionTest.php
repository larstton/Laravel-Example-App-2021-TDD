<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\ExtendTrialAction;
use App\Models\Recipient;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ExtendTrialActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_extend_trial_for_team()
    {
        $team = $this->createTeam([
            'created_at'    => now()->subDays(15),
            'trial_ends_at' => $trialEndsAt = now(),
        ]);
        $user = $this->createUser($team);

        $team = resolve(ExtendTrialAction::class)->execute($user, $team);

        $team->refresh();

        $this->assertDateTimesMatch($team->trial_ends_at, $trialEndsAt->addDays(15));
    }

    /** @test */
    public function wont_extend_if_trial_not_at_a_given_point()
    {
        $team = $this->createTeam([
            'created_at'    => now()->subDays(10),
            'trial_ends_at' => $trialEndsAt = now(),
        ]);
        $user = $this->createUser($team);

        $team = resolve(ExtendTrialAction::class)->execute($user, $team);

        $team->refresh();

        $this->assertDateTimesDoNotMatch($team->trial_ends_at, $trialEndsAt->addDays(15));
    }

    /** @test */
    public function will_log_to_activity()
    {
        $team = $this->createTeam([
            'created_at'    => now()->subDays(15),
            'trial_ends_at' => $trialEndsAt = now(),
        ]);
        $user = $this->createUser($team);

        resolve(ExtendTrialAction::class)->execute($user, $team);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => $user->id,
            'subject_id'   => $team->id,
            'subject_type' => Team::class,
            'description'  => 'Trial extended for 15 days.',
        ]);
    }
}
