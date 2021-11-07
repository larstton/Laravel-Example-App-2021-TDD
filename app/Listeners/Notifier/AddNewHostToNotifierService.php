<?php

namespace App\Listeners\Notifier;

use App\Events\Host\HostCreated;
use App\Support\NotifierService;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddNewHostToNotifierService implements ShouldQueue
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function handle(HostCreated $event)
    {
        $this->notifierService->addHost($event->host);
    }
}
