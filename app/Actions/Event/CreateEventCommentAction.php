<?php

namespace App\Actions\Event;

use App\Data\Event\EventCommentData;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\User;

class CreateEventCommentAction
{
    public function execute(User $user, Event $event, EventCommentData $eventCommentData): EventComment
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $event->eventComments()->create([
            'text'              => strip_tags($eventCommentData->text),
            'user_id'           => $user->id,
            'team_id'           => $user->team_id,
            'nickname'          => $user->nickname,
            'visible_to_guests' => $eventCommentData->visibleToGuests,
            'statuspage'        => $eventCommentData->statuspage,
            'forward'           => $eventCommentData->forward,
        ]);
    }
}
