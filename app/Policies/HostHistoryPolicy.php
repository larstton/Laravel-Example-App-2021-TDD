<?php

namespace App\Policies;

use App\Enums\TeamPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostHistoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->team->isOnTrial() || $user->team->isOnPaidPlan();
    }
}
