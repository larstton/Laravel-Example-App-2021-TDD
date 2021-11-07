<?php

namespace App\Events\Team;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamSettingsUpdated
{
    use Dispatchable, SerializesModels;

    public Team $team;
    public array $settings;

    public function __construct(Team $team, array $settings)
    {
        $this->team = $team;
        $this->settings = $settings;
    }
}
