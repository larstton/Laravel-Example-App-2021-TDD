<?php

namespace App\Http\Klick\Controllers\Recipient;

use App\Actions\Recipient\VerifyRecipientAction;
use App\Http\Controllers\Controller;
use App\Models\Recipient;

class RecipientVerificationController extends Controller
{
    public function __invoke(VerifyRecipientAction $verifyRecipientAction, Recipient $recipient, string $token)
    {
        $recipient = $verifyRecipientAction->execute($recipient, $token);

        return view('klick.pages.recipient-verification', compact('recipient'));
    }
}
