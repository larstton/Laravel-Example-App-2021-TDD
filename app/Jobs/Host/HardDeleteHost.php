<?php

namespace App\Jobs\Host;

use App\Models\Host;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HardDeleteHost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $hostId;

    public function __construct(Host $host)
    {
        $this->hostId = $host->id;
    }

    public function handle()
    {
        if (is_null($host = Host::onlyTrashed()->find($this->hostId))) {
            $this->fail();

            return;
        }

        $host->forceDelete();
    }
}
