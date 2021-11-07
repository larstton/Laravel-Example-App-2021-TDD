<?php

namespace App\Data\Recipient;

use App\Data\BaseData;
use App\Enums\RecipientMediaType;
use App\Http\Requests\Recipient\SendTestMessageRequest;
use App\Models\Recipient;

class TestMessageData extends BaseData
{
    public RecipientMediaType $mediatype;
    public string $sendto;
    public ?string $message;
    public ?string $option1;
    public ?array $extraData;

    public static function fromRequest(SendTestMessageRequest $sendTestMessageRequest): self
    {
        return new self([
            'mediatype' => RecipientMediaType::coerce($sendTestMessageRequest->mediatype),
            'sendto'    => $sendTestMessageRequest->sendto,
            'option1'   => $sendTestMessageRequest->option1,
            'message'   => $sendTestMessageRequest->message ?? 'This is a test message from CloudRadar.',
            'extraData' => $sendTestMessageRequest->extraData,
        ]);
    }

    public static function fromRecipient(Recipient $recipient): self
    {
        return new self([
            'mediatype' => $recipient->media_type,
            'sendto'    => $recipient->sendto,
            'option1'   => $recipient->option1,
            'message'   => 'This is a test message from CloudRadar.',
            'extraData' => $recipient->extraData,
        ]);
    }
}
