<?php

namespace App\Events\Event;

use App\Models\Event;
use Illuminate\Foundation\Events\Dispatchable;

class EventDeleted
{
    use Dispatchable;

    /**
     * @var Event
     */
    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
