<?php

namespace App\Http\Klick\Controllers;

use App\Actions\Recipient\MuteRecipientAction;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Recipient;

class MuteRecipientController extends Controller
{
    public function edit($recipient, $event)
    {
        $recipient = Recipient::find($recipient);
        if (filled($recipient)) {
            $event = Event::whereId($event)->whereTeamId($recipient->team_id)->first();
        }

        if (filled($recipient) && filled($event)) {
            return view('klick.pages.recipient.mute-recipient-form', compact('recipient', 'event'));
        }

        return view('klick.pages.general-error');
    }

    public function update(Recipient $recipient, $event, MuteRecipientAction $muteRecipientAction)
    {
        $event = Event::whereId($event)->whereTeamId($recipient->team_id)->firstOrFail();

        $recipient = $muteRecipientAction->execute($recipient);

        return view('klick.pages.recipient.mute-recipient-result', compact('recipient', 'event'));
    }
}
