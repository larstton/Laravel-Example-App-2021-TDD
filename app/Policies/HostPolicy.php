<?php

namespace App\Policies;

use App\Enums\TeamPlan;
use App\Models\Concerns\AuthedEntity;
use App\Models\Host;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthedEntity $authedEntity)
    {
        return true;
    }

    public function view(AuthedEntity $authedEntity, Host $host)
    {
        return (string) $host->team_id === (string) $authedEntity->team->id;
    }

    public function create(AuthedEntity $authedEntity)
    {
        // TODO - move to a middleware to protect routes
        if ($authedEntity->team->plan->is(TeamPlan::Frozen())) {
            return $this->deny('Your trial has ended. Please upgrade your plan.');
        }
        if ($authedEntity->team->hosts()->active()->count() >= $authedEntity->team->max_hosts) {
            return $this->deny('Maximum of allowed hosts reached.');
        }

        return true;
    }

    public function update(AuthedEntity $authedEntity, Host $host)
    {
        // TODO - move to a middleware to protect routes
        if ($authedEntity->team->plan->is(TeamPlan::Frozen())) {
            return $this->deny('Your trial has ended. Please upgrade your plan.');
        }

        // Host belongs to the team the authenticated user belongs to...
        return (string) $host->team_id === (string) $authedEntity->team->id;
    }

    public function delete(AuthedEntity $authedEntity, Host $host)
    {
        // Host belongs to the team the authenticated user belongs to...
        return (string) $host->team_id === (string) $authedEntity->team->id;
    }
}
