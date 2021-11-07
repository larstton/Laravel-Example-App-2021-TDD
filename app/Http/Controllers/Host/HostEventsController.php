<?php

namespace App\Http\Controllers\Host;

use App\Actions\Event\DeleteEventAction;
use App\Http\Controllers\Controller;
use App\Http\Queries\HostEventQuery;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use App\Models\Host;

class HostEventsController extends Controller
{
    public function index(Host $host)
    {
        $this->authorize('viewAny', [Event::class]);

        return EventResource::collection(
            (new HostEventQuery($host))->get()
        );
    }

    public function destroy(Host $host, Event $event, DeleteEventAction $deleteEventAction)
    {
        $this->authorize($event);

        $deleteEventAction->execute($event);

        return $this->noContent();
    }
}
