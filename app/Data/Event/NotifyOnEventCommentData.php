<?php

namespace App\Data\Event;

use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class NotifyOnEventCommentData extends FlexibleDataTransferObject
{
    public string $nickname;
    public int $timestamp;
    public string $timezone;
    public string $text;
    public string $eventUuid;
    public ?array $recipients;

    public function addRecipient(Recipient $recipient)
    {
        // TODO
        // This data object is invalid and needs refactoring

        $this->recipients[] = [
            'mediatype'     => $recipient->media_type->value,
            'sendto'        => $recipient->sendto,
            'option1'       => $recipient->option1,
            'uuid'          => $recipient->id,
            'sendEventUuid' => $recipient->event_uuids,
            'extraData'     => $recipient->extra_data,
        ];
    }
}
