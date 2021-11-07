<?php

namespace App\Http\Controllers\Recipient;

use App\Actions\Recipient\SendTestMessageAction;
use App\Data\Recipient\TestMessageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Recipient\SendTestMessageRequest;

class SendTestMessageController extends Controller
{
    public function __invoke(
        SendTestMessageRequest $sendTestMessageRequest,
        SendTestMessageAction $sendTestMessageAction
    ) {
        $result = $sendTestMessageAction->execute(
            TestMessageData::fromRequest($sendTestMessageRequest)
        );

        if (! $result) {
            $this->errorBadRequest();
        }

        return $this->accepted();
    }
}
