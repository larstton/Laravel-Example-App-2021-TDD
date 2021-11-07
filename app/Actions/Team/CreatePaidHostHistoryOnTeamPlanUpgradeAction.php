<?php

namespace App\Actions\Team;

use App\Models\HostHistory;
use App\Models\Team;

class CreatePaidHostHistoryOnTeamPlanUpgradeAction
{
    public function execute(Team $team)
    {
        $data = HostHistory::whereTeamId($team->id)
            ->whereNotPaid()
            ->get()
            ->map(function (HostHistory $history) {
                return [
                    'host_id'    => $history->host_id,
                    'team_id'    => $history->team_id,
                    'user_id'    => $history->user_id,
                    'name'       => $history->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'paid'       => true,
                ];
            });

        HostHistory::insert($data->all());
    }
}
