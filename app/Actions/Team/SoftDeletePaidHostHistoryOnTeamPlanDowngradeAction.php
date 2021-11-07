<?php

namespace App\Actions\Team;

use App\Models\HostHistory;
use App\Models\Team;

class SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction
{
    public function execute(Team $team)
    {
        HostHistory::whereTeamId($team->id)
            ->whereIsPaid()
            ->get()
            ->each->delete();
    }
}
