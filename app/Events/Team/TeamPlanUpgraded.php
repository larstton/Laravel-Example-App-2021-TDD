<?php

namespace App\Events\Team;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamPlanUpgraded
{
    use Dispatchable, SerializesModels;

    public $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
