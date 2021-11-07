<?php

namespace App\Http\Controllers\Recipient;

use App\Enums\RecipientMediaType;
use App\Http\Controllers\Controller;
use App\Models\Recipient;
use App\Notifications\RecipientEmailAddressConfirmationNotification;

class ResendVerificationEmailController extends Controller
{
    public function __invoke(Recipient $recipient)
    {
        if ($recipient->media_type->is(RecipientMediaType::Email()) && ! $recipient->isVerified()) {
            $recipient->notify(new RecipientEmailAddressConfirmationNotification);

            return $this->accepted();
        }

        $this->errorMethodNotAllowed('You cannot verifiy a verified email.');
    }
}
