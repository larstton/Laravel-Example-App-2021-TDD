<?php

namespace App\Actions\Team;

use App\Jobs\Team\HardDeleteTeam;
use App\Jobs\Team\PostDeleteTeamTidyUp;
use App\Models\Team;

class DeleteTeamAction
{
    public function execute(Team $team)
    {
        $team->delete();
        PostDeleteTeamTidyUp::withChain([
            new HardDeleteTeam($team),
        ])->dispatch($team);
    }
}
