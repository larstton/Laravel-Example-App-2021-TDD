<?php

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class PurgeEventsForHostAction
{
    public function execute(Team $team, $hostId)
    {
        $shouldFlushCache = false;

        Event::query()
            ->withoutGlobalScopes()
            ->whereHostId($hostId)
            ->each(function (Event $event) use (&$shouldFlushCache) {
                dd($event);
                $event->eventComments()->delete();
                $event->sentReminders()->delete();
                $event->forceDelete();
                $shouldFlushCache = true;
            });

            if ($shouldFlushCache) {
                Cache::tags($team->getReportCacheTag())->flush();
            }
    }
}
