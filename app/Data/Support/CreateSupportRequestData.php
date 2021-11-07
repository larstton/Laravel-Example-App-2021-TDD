<?php

namespace App\Data\Support;

use App\Data\BaseData;
use App\Http\Requests\Support\CreateSupportRequest;

class CreateSupportRequestData extends BaseData
{
    public string $body;
    public string $subject;
    public array $attachment = [];

    public static function fromRequest(CreateSupportRequest $request): self
    {
        return new self([
            'subject'    => $request->subject,
            'body'       => $request->body,
            'attachment' => $request->attachment ?? [],
        ]);
    }
}
