<?php

namespace App\Http\Klick\Controllers;

use App\Actions\Recipient\CancelRemindersForEventAction;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Recipient;

class CancelRemindersForEventController extends Controller
{
    public function __invoke($recipient, $event, CancelRemindersForEventAction $cancelRemindersForEventAction)
    {
        $recipient = Recipient::find($recipient);
        if (filled($recipient)) {
            $event = Event::whereId($event)->whereTeamId($recipient->team_id)->first();
        }

        if (filled($recipient) && filled($event)) {
            $event = $cancelRemindersForEventAction->execute($event, $recipient);

            return view('klick.pages.recipient.cancel-reminders-result', compact('recipient', 'event'));
        }

        return view('klick.pages.general-error');
    }

}
