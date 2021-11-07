<?php

namespace App\Policies;

use App\Models\Frontman;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FrontmanPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if (! $user->isTeamMember()) {
            return false;
        }
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Frontman $frontman)
    {
        return $user->team_id === $frontman->team_id;
    }

    public function create(User $user)
    {
        if ($user->team->frontmen()->count() >= $user->team->max_frontmen) {
            $this->deny("Maximum of allowed {$user->team->max_frontmen} frontmen reached.");
        }

        return true;
    }

    public function update(User $user, Frontman $frontman)
    {
        return $user->team_id === $frontman->team_id;
    }

    public function delete(User $user, Frontman $frontman)
    {
        if ($frontman->hosts()->count() > 0) {
            $this->deny('This frontman is still in use. Detach all hosts first.');
        }

        return $user->team_id === $frontman->team_id;
    }
}
