<?php

namespace App\Actions\Event;

use App\Enums\EventState;
use App\Models\Event;
use App\Support\NotifierService;

class RecoverEventOnNotifierAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Event $event)
    {
        if ($event->state->is(EventState::Active())) {
            $this->notifierService->recoverEvent($event);
        }
    }
}
