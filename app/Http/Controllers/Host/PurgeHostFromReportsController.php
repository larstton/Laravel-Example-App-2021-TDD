<?php

namespace App\Http\Controllers\Host;

use App\Actions\Event\PurgeEventsForHostAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurgeHostFromReportsController extends Controller
{
    public function __invoke(Request $request, PurgeEventsForHostAction $purgeEventsForHostAction, $hostId)
    {
        $purgeEventsForHostAction->execute(current_team(), $hostId);

        return $this->noContent();
    }
}
