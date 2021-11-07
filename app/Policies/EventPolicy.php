<?php

namespace App\Policies;

use App\Models\Concerns\AuthedEntity;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthedEntity $authedEntity)
    {
        return true;
    }

    public function view(AuthedEntity $authedEntity, Event $event)
    {
        return $authedEntity->team->id === $event->team_id;
    }

    public function update(AuthedEntity $authedEntity, Event $event)
    {
        return $authedEntity->team->id === $event->team_id;
    }

    public function delete(AuthedEntity $authedEntity, Event $event)
    {
        return $authedEntity->team->id === $event->team_id;
    }
}
