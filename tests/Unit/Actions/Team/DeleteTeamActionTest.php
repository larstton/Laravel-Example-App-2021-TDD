<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\DeleteTeamAction;
use App\Jobs\Team\HardDeleteTeam;
use App\Jobs\Team\PostDeleteTeamTidyUp;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DeleteTeamActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_team()
    {
        Bus::fake();

        $team = $this->createTeam();

        resolve(DeleteTeamAction::class)->execute($team);

        $this->assertSoftDeleted($team);
    }

    /** @test */
    public function will_dispatch_job_to_tidy_up_and_hard_delete_team()
    {
        Bus::fake();

        $team = $this->createTeam();

        resolve(DeleteTeamAction::class)->execute($team);

        Bus::assertChained([
            PostDeleteTeamTidyUp::class,
            HardDeleteTeam::class,
        ]);
    }
}
