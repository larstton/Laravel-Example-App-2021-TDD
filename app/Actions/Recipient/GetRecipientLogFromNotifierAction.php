<?php

namespace App\Actions\Recipient;

use App\Models\Recipient;
use App\Support\NotifierService;

class GetRecipientLogFromNotifierAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Recipient $recipient, $parameters)
    {
        return $this->notifierService->getRecipientLogs($recipient, $parameters);
    }
}
