<?php

namespace App\Listeners\Host;

use App\Actions\Host\LogHostHistoryAction;
use App\Events\Host\HostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddNewHostToHistoryLog implements ShouldQueue
{
    private $logHostHistoryAction;

    public function __construct(LogHostHistoryAction $logHostHistoryAction)
    {
        $this->logHostHistoryAction = $logHostHistoryAction;
    }

    public function handle(HostCreated $event)
    {
        $this->logHostHistoryAction->execute($event->host, 'create');
    }
}
