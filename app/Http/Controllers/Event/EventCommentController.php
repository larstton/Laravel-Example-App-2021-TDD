<?php

namespace App\Http\Controllers\Event;

use App\Actions\Event\CreateEventCommentAction;
use App\Data\Event\EventCommentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventCommentRequest;
use App\Http\Resources\Event\EventCommentResource;
use App\Models\Event;
use App\Models\EventComment;

class EventCommentController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize(EventComment::class);

        $query = $event->eventComments()
            ->when(current_user()->isGuest(), function ($query) {
                $query->where('visible_to_guests', true);
            })
            ->orderBy('created_at', 'desc');

        return EventCommentResource::collection($query->jsonPaginate());
    }

    public function store(CreateEventCommentRequest $request, Event $event, CreateEventCommentAction $createEvent)
    {
        $this->authorize('create', [EventComment::class, $event]);

        $eventComment = $createEvent->execute(
            $this->user(),
            $event,
            EventCommentData::fromRequest($request)
        );

        return EventCommentResource::make($eventComment);
    }
}
