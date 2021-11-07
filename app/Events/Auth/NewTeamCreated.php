<?php

namespace App\Events\Auth;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTeamCreated
{
    use Dispatchable, SerializesModels;

    public User $user;

    public Team $team;

    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }
}
