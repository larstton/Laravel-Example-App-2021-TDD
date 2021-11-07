<?php

namespace App\Http\Klick\Controllers;

use App\Actions\Recipient\MuteCommentNotificationsForRecipientAction;
use App\Http\Controllers\Controller;
use App\Models\Recipient;

class MuteCommentsForRecipientController extends Controller
{
    public function edit($recipient)
    {
        if (filled($recipient = Recipient::find($recipient))) {
            return view('klick.pages.recipient.mute-comments-form', compact('recipient'));
        }

        return view('klick.pages.general-error');
    }

    public function update(Recipient $recipient, MuteCommentNotificationsForRecipientAction $action)
    {
        $recipient = $action->execute($recipient);

        return view('klick.pages.recipient.mute-comments-result', compact('recipient'));
    }
}
