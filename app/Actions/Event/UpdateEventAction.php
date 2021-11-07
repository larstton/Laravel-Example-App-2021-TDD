<?php

namespace App\Actions\Event;

use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Models\Event;
use App\Support\NotifierService;

class UpdateEventAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Event $event, EventReminder $reminders, EventState $state)
    {
        $event->update([
            'state'     => $state,
            'reminders' => $reminders,
        ]);

        if (! $reminders) {
            $this->notifierService->deleteRemindersForEvent($event);
        }
    }
}
