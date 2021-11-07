<?php

namespace App\Http\Klick\Controllers\Recipient;

use App\Actions\Recipient\UnsubscribeRecipientFromDailySummaryAction;
use App\Http\Controllers\Controller;
use App\Models\Recipient;

class RecipientUnsubscribeDailySummaryController extends Controller
{
    public function __invoke(UnsubscribeRecipientFromDailySummaryAction $action, Recipient $recipient)
    {
        $recipient = $action->execute($recipient);

        return view('klick.pages.recipient-unsubscribe', compact('recipient'));
    }
}
