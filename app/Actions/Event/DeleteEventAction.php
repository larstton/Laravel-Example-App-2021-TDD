<?php

namespace App\Actions\Event;

use App\Models\Event;
use App\Support\NotifierService;

class DeleteEventAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Event $event)
    {
        $this->notifierService->recoverEvent($event);
        $event->delete();
    }
}
