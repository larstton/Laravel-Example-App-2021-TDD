<?php

namespace App\Actions\Recipient;

use App\Enums\EventReminder;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\Recipient;

class CancelRemindersForEventAction
{
    public function execute(Event $event, Recipient $recipient): Event
    {
        $recipient->team->makeCurrentTenant();
        $event->update([
            'reminders' => EventReminder::Disabled(),
        ]);

        activity()
            ->causedByAnonymous()
            ->on($event)
            ->tap(function (ActivityLog $activity) use ($recipient) {
                $activity->team_id = $recipient->team_id;
            })
            ->log(sprintf("Reminder for event \"%s\" disabled", $event->meta['name']));

        return $event;
    }
}
