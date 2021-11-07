<?php

namespace App\Http\Controllers\Event;

use App\Actions\Event\DeleteEventAction;
use App\Actions\Event\UpdateEventAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Http\Controllers\Controller;
use App\Http\Queries\EventQuery;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function index(EventQuery $eventQuery)
    {
        $this->authorize(Event::class);

        return EventResource::collection($eventQuery->jsonPaginate());
    }

    public function update(UpdateEventRequest $request, Event $event, UpdateEventAction $updateEventAction)
    {
        $this->authorize($event);

        // TODO refactor this to a role+permission setup and add as route middleware.
        Gate::authorize('role-team-member');

        $updateEventAction->execute(
            $event,
            EventReminder::coerce((int) $request->reminders),
            EventState::coerce((int) $request->state)
        );

        return $this->accepted();
    }

    public function destroy(Event $event, DeleteEventAction $deleteEventAction)
    {
        $this->authorize($event);

        $deleteEventAction->execute($event);

        return $this->noContent();
    }
}
