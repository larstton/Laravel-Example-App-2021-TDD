<?php

namespace App\Listeners\Host;

use App\Actions\Host\LogHostHistoryAction;
use App\Events\Host\HostUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateHostInHistoryLog implements ShouldQueue
{
    private $logHostHistoryAction;

    public function __construct(LogHostHistoryAction $logHostHistoryAction)
    {
        $this->logHostHistoryAction = $logHostHistoryAction;
    }

    public function handle(HostUpdated $event)
    {
        $this->logHostHistoryAction->execute($event->host, 'update');
    }
}
