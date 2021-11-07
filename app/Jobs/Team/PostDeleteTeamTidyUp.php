<?php

namespace App\Jobs\Team;

use App\Actions\Host\PostHostDeleteTidyUpAction;
use App\Actions\Team\PostTeamDeleteTidyUpAction;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostDeleteTeamTidyUp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $teamId;

    public function __construct(Team $team)
    {
        $this->teamId = $team->id;
    }

    public function handle(PostTeamDeleteTidyUpAction $teamDeleteTidyUpAction)
    {
        if (is_null($team = Team::onlyTrashed()->find($this->teamId))) {
            $this->fail();

            return;
        }

        $teamDeleteTidyUpAction->execute($team);
    }
}
