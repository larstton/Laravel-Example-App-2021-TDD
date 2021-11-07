<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Notifications\Team\TeamPlanUpgradedNotification;

class NotifyAdminsTeamPlanUpgradedAction
{
    public function execute(Team $team)
    {
        $team->admins->each->notify(new TeamPlanUpgradedNotification($team));
    }
}
