<?php

namespace App\Actions\Recipient;

use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Notifications\RecipientEmailAddressConfirmationNotification;

class SendRecipientEmailAddressConfirmationAction
{
    public function execute(Recipient $recipient)
    {
        if ($recipient->media_type->is(RecipientMediaType::Email())) {
            $recipient->notify(new RecipientEmailAddressConfirmationNotification);
        }
    }
}
