<?php

namespace App\Events\Event;

use App\Models\EventComment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCommentCreated
{
    use Dispatchable, SerializesModels;

    public $eventComment;

    public function __construct(EventComment $eventComment)
    {
        $this->eventComment = $eventComment;
    }
}
