<?php

namespace App\Data\Event;

use App\Data\BaseData;
use App\Http\Requests\Event\CreateEventCommentRequest;

class EventCommentData extends BaseData
{
    public string $text;
    public bool $visibleToGuests;
    public bool $statuspage;
    public bool $forward;

    public static function fromRequest(CreateEventCommentRequest $request)
    {
        return new self([
            'text'            => $request->text,
            'visibleToGuests' => (bool) $request->input('visibleToGuests', true),
            'statuspage'      => (bool) $request->input('statuspage', false),
            'forward'         => (bool) $request->input('forward', true),
        ]);
    }
}
