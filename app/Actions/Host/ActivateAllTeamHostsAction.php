<?php

namespace App\Actions\Host;

use App\Enums\HostActiveState;
use App\Models\Team;

class ActivateAllTeamHostsAction
{
    public function execute(Team $team)
    {
        $team->hosts->each->update([
            'active' => HostActiveState::Active(),
        ]);
    }
}
