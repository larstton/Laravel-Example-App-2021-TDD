<?php

namespace App\Listeners\Event;

use App\Actions\Event\RecoverEventOnNotifierAction;
use App\Events\Event\EventDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecoverDeletedEvent implements ShouldQueue
{
    private RecoverEventOnNotifierAction $action;

    public function __construct(RecoverEventOnNotifierAction $action)
    {
        $this->action = $action;
    }

    public function handle(EventDeleted $event)
    {
        $this->action->execute($event->event);
    }
}
