<?php

namespace App\Actions\Recipient;

use App\Data\Recipient\TestMessageData;
use App\Support\NotifierService;

class SendTestMessageAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(TestMessageData $data): bool
    {
        return $this->notifierService->sendTestMessage([
            'mediatype' => $data->mediatype->value,
            'sendto'    => $data->sendto,
            'option1'   => $data->option1,
            'message'   => $data->message,
            'extraData' => $data->extraData,
        ]);
    }
}
