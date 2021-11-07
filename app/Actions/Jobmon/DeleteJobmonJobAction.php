<?php

namespace App\Actions\Jobmon;

use App\Models\Event;
use App\Models\Host;
use App\Models\JobmonResult;
use App\Support\NotifierService;

class DeleteJobmonJobAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Host $host, string $jobId)
    {
        $host->jobmonResults()
            ->whereJobId($jobId)
            ->each(function (JobmonResult $jobmonResult) use ($jobId, $host) {
                Event::whereActiveEventForHostAndJobId($host, $jobId)
                    ->each(function (Event $event) {
                        $this->notifierService->recoverEvent($event);
                        $event->delete();
                    });

                $jobmonResult->delete();
            });
    }
}
