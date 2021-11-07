<?php

namespace App\Actions\Host;

use App\Models\Host;
use App\Support\NotifierService;

class GetHostAlertLogFromNotifierAction
{
    private NotifierService $notifierService;

    public function __construct(NotifierService $notifierService)
    {
        $this->notifierService = $notifierService;
    }

    public function execute(Host $host, array $parameters)
    {
        return $this->notifierService->getHostAlertLogs($host, $parameters);
    }
}
