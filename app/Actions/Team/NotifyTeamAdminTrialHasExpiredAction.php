<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Notifications\Team\TrialExpiredDowngradedNotification;

class NotifyTeamAdminTrialHasExpiredAction
{
    public function execute(Team $team)
    {
        $team->admins->each(function ($admin) use ($team) {
            $admin->notify(new TrialExpiredDowngradedNotification($team));
        });
    }
}
