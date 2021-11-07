<?php

namespace App\Actions\Host;

use App\Jobs\Host\HardDeleteHost;
use App\Jobs\Host\PostDeleteHostTidyUp;
use App\Models\Host;

class DeleteHostAction
{
    public function execute(Host $host, $purgeReports = false)
    {
        $hostSoftDeleted = $host->delete();

        if ($hostSoftDeleted) {
            PostDeleteHostTidyUp::withChain([
                new HardDeleteHost($host),
            ])->dispatch($host, $purgeReports);
        }
    }
}
