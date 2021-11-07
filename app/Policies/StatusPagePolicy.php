<?php

namespace App\Policies;

use App\Models\StatusPage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, StatusPage $statusPage)
    {
        return $user->team_id === $statusPage->team_id;
    }

    public function create(User $user, ?StatusPage $statusPage = null)
    {
        if (is_null($statusPage)) {
            return true;
        }

        return $user->team_id === $statusPage->team_id;
    }

    public function update(User $user, StatusPage $statusPage)
    {
        return $user->team_id === $statusPage->team_id;
    }

    public function delete(User $user, StatusPage $statusPage)
    {
        return $user->team_id === $statusPage->team_id;
    }
}
