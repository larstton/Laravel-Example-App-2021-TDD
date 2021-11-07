<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Models\User;
use App\Notifications\Team\TeamPlanDowngradedNotification;

class NotifyAdminsTeamPlanDowngradedAction
{
    public function execute(Team $team)
    {
        $team->admins->each(function (User $admin) use ($team) {
            $admin->notify(new TeamPlanDowngradedNotification($team));
        });
    }
}
