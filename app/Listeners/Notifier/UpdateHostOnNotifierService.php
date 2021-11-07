<?php

namespace App\Listeners\Notifier;

use App\Events\Host\HostUpdated;
use App\Support\NotifierService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateHostOnNotifierService implements ShouldQueue
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function handle(HostUpdated $event)
    {
        $this->notifierService->updateHost($event->host);
    }
}
