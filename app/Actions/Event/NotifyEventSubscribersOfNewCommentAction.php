<?php

namespace App\Actions\Event;

use App\Data\Event\NotifyOnEventCommentData;
use App\Models\EventComment;
use App\Models\Recipient;
use App\Notifications\NewCommentAddedRecipientNotification;
use App\Support\NotifierService;

class NotifyEventSubscribersOfNewCommentAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(EventComment $eventComment)
    {
        if (! $eventComment->forward) {
            return;
        }

        $payload = new NotifyOnEventCommentData([
            'nickname'  => $eventComment->user->nickname,
            'timestamp' => now()->unix(),
            'timezone'  => $eventComment->user->team->timezone,
            'text'      => $eventComment->text,
            'eventUuid' => $eventComment->event_id,
        ]);

        Recipient::active()->whereSubscribedToComments()->verified()
            ->get()
            ->each(function (Recipient $recipient) use ($payload, $eventComment) {
                $payload->addRecipient($recipient);
                $recipient->notify(new NewCommentAddedRecipientNotification($eventComment, $recipient));
            })
            ->whenNotEmpty(fn () => $this->notifierService->sendEventComment($payload));
    }
}
