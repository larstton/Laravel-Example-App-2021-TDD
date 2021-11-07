<?php

namespace App\Actions\Host;

use App\Enums\TeamPlan;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\Team;
use App\Models\WebCheck;

class UpdateCheckIntervalsForHostChecksAction
{
    public function execute(Team $team)
    {
        WebCheck::whereIn('host_id', Host::query()->select('id')->whereTeamId($team->id))
            ->get()
            ->each->update([
                'check_interval' => $team->min_check_interval,
            ]);
        ServiceCheck::whereIn('host_id', Host::query()->select('id')->whereTeamId($team->id))
            ->get()
            ->each->update([
                'check_interval' => $team->min_check_interval,
            ]);
        SnmpCheck::whereIn('host_id', Host::query()->select('id')->whereTeamId($team->id))
            ->get()
            ->each->update([
                'check_interval' => $team->min_check_interval,
            ]);
    }
}
