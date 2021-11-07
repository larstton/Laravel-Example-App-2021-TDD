<?php

namespace App\Listeners\Recipient;

use App\Data\Recipient\TestMessageData;
use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientCreated;
use App\Support\NotifierService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendIntegromatInitMessage implements ShouldQueue
{
    private NotifierService $notifier;

    /**
     * SendIntegromatInitMessage constructor.
     * @param  NotifierService  $notifier
     */
    public function __construct(NotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handle(RecipientCreated $event)
    {
        $recipient = $event->recipient;
        if ($recipient->media_type->is(RecipientMediaType::Integromat())) {
            $this->notifier->sendTestMessage(TestMessageData::fromRecipient($recipient));
        }
    }
}
