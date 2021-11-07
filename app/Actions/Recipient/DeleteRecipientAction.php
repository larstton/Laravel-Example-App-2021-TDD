<?php

namespace App\Actions\Recipient;

use App\Models\Recipient;
use App\Support\NotifierService;

class DeleteRecipientAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Recipient $recipient)
    {
        $this->notifierService->deleteRecipient($recipient);

        // TODO delete reminders

        $recipient->delete();
    }
}
