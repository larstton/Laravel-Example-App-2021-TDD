<?php

namespace App\Policies;

use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamMemberPolicy
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

    public function view(User $user, TeamMember $teamMember)
    {
        return $user->team_id === $teamMember->team_id;
    }

    public function create(User $user)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please confirm your email address to invite team-mates to your monitoring account.');
        }

        if (TeamMember::count() >= $user->team->max_members) {
            return $this->deny("Your account has {$user->team->max_members} team members, which is the maximum number of team members permitted for your team.");
        }

        return true;
    }

    public function update(User $user, TeamMember $teamMember)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please confirm your email address to make changes to your team.');
        }

        if ($teamMember->isTeamAdmin()
            && request('role') !== TeamMemberRole::Admin
            && $teamMember->team_status->is(TeamStatus::Joined())
            && TeamMember::activeAdmin()->count() === 1
        ) {
            // This member is about to be degraded. Check if the team remains with at least one admin.
            return $this->deny('This team member is the last remaining admin and therefore it cannot be downgraded. A team needs at least one admin.');
        }

        return $user->team_id === $teamMember->team_id;
    }

    public function delete(User $user, TeamMember $teamMember)
    {
        if ($user->isTeamAdmin() && ! $user->hasVerifiedEmail()) {
            return $this->deny('Please confirm your email address to make changes to your team.');
        }

        if ($user->id === $teamMember->id) {
            return $this->deny('You cannot delete yourself. Cancel the entire account instead.');
        }

        if ($teamMember->isTeamAdmin() && $teamMember->team_status->is(TeamStatus::Joined()) && TeamMember::activeAdmin()->count() === 1) {
            return $this->deny('You cannot delete this team member as they are the last remaining joined admin. A team needs at least one active admin to remain accessible.');
        }

        return $user->team_id === $teamMember->team_id;
    }
}
