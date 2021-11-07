<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class EventCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  Event  $event
     * @return mixed
     */
    public function create(User $user, Event $event)
    {
        Gate::authorize('role-team-member');

        return ! is_null($user->nickname) && $user->team_id === $event->team_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  EventComment  $eventComment
     * @return mixed
     */
    public function update(User $user, EventComment $eventComment)
    {
        return $user->team_id === $eventComment->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  EventComment  $eventComment
     * @return mixed
     */
    public function delete(User $user, EventComment $eventComment)
    {
        return $user->team_id === $eventComment->team_id;
    }
}
