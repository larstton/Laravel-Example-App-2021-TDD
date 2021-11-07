<?php

namespace App\Listeners\Recipient;

use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientCreated;
use App\Notifications\RecipientEmailAddressConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRecipientEmailConfirmation implements ShouldQueue
{
    public function handle(RecipientCreated $event)
    {
        $recipient = $event->recipient;

        if ($recipient->media_type->isNot(RecipientMediaType::Email())) {
            return;
        }

        if (! $recipient->active) {
            return;
        }

        if ($recipient->isVerified()) {
            return;
        }

        $recipient->notify(new RecipientEmailAddressConfirmationNotification);
    }
}
