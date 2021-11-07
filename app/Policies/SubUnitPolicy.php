<?php

namespace App\Policies;

use App\Models\SubUnit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubUnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, SubUnit $subUnit)
    {
        return $user->team_id === $subUnit->team_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, SubUnit $subUnit)
    {
        return $user->team_id === $subUnit->team_id;
    }

    public function delete(User $user, SubUnit $subUnit)
    {
        return $user->team_id === $subUnit->team_id;
    }
}
