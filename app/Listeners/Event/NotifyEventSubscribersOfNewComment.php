<?php

namespace App\Listeners\Event;

use App\Actions\Event\NotifyEventSubscribersOfNewCommentAction;
use App\Events\Event\EventCommentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEventSubscribersOfNewComment implements ShouldQueue
{
    public function __construct(NotifyEventSubscribersOfNewCommentAction $action)
    {
        $this->notifyUserAction = $action;
    }

    public function handle(EventCommentCreated $event)
    {
        $this->notifyUserAction->execute($event->eventComment);
    }
}
