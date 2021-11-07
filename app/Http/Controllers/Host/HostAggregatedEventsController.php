<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Queries\HostAggregatedEventQuery;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class HostAggregatedEventsController extends Controller
{
    public function __invoke(Request $request, HostAggregatedEventQuery $query)
    {
        $this->authorize('viewAny', [Event::class]);

        return EventResource::collection($query->get());
    }
}
