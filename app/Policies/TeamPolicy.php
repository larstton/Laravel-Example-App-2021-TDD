<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Team $team)
    {
        return $user->team_id === $team->id;
    }

    public function update(User $user, Team $team)
    {
        return $user->team_id === $team->id;
    }
}
