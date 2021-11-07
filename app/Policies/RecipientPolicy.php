<?php

namespace App\Policies;

use App\Enums\TeamPlan;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Recipient $recipient)
    {
        return true;
    }

    public function create(User $user)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please verify your email address to activate new alerts.');
        }

        // TODO - move to a middleware to protect routes
        if ($user->team->plan->is(TeamPlan::Frozen())) {
            return $this->deny('Your trial has ended. Please upgrade your plan.');
        }

        if (Recipient::count() >= current_team()->max_recipients) {
            return $this->deny('Maximum of allowed recipients reached.');
        }

        return true;
    }

    public function update(User $user, Recipient $recipient)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please verify your email address to make changes to your existing recipients.');
        }

        // TODO - move to a middleware to protect routes
        if ($user->team->plan->is(TeamPlan::Frozen())) {
            return $this->deny('Your trial has ended. Please upgrade your plan.');
        }

        // Recipient belongs to the team the authenticated user belongs to...
        return $recipient->team_id === $user->team->id;
    }

    public function delete(User $user, Recipient $recipient)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please verify your email address to make changes to your existing recipients.');
        }

        // Recipient belongs to the team the authenticated user belongs to...
        return $recipient->team_id === $user->team->id;
    }
}
