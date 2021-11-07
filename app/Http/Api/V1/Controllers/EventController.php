<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EventCollection;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

class EventController extends Controller
{
    public function __invoke()
    {
        $this->authorize('viewAny', Event::class);

        $data = Event::query()
            ->when(
                ($state = request('filterState', false)) && is_numeric($state),
                fn (Builder $query) => $query->where('events.state', (int) $state)
            )
            ->when(
                $hostUuid = request('filterHostUuid'),
                fn (Builder $query) => $query->where('events.meta->affectedHost->uuid', $hostUuid)
            )
            ->when(
                request('includeComments', false),
                fn (Builder $query) => $query->with('eventComments')
            )
            ->get();

        return EventCollection::make($data);
    }
}
