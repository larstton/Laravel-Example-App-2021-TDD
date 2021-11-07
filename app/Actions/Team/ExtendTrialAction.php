<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Models\User;

class ExtendTrialAction
{
    public function execute(User $user, Team $team): Team
    {
        $team->makeCurrentTenant();

        // Check that current trial period is 15 days so team does not extend it twice.
        if ($team->created_at->addDays(15)->equalTo($team->trial_ends_at)) {
            activity()
                ->causedBy($user)
                ->on($team)
                ->tap(fn ($activity) => $activity->team_id = $user->team_id)
                ->log('Trial extended for 15 days.');

            $team->update([
                'trial_ends_at' => now()->addDays(15),
            ]);
        }

        return $team;
    }
}
