<?php

namespace App\Jobs\Team;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HardDeleteTeam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $teamId;

    public function __construct(Team $team)
    {
        $this->teamId = $team->id;
    }

    public function handle()
    {
        if (is_null($team = Team::onlyTrashed()->find($this->teamId))) {
            $this->fail();

            return;
        }

        $team->forceDelete();
    }
}
