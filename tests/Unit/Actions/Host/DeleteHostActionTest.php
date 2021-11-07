<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\DeleteHostAction;
use App\Events\Host\HostDeleted;
use App\Jobs\Host\HardDeleteHost;
use App\Jobs\Host\PostDeleteHostTidyUp;
use App\Models\Host;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteHostActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_soft_delete_host()
    {
        Bus::fake();

        $team = $this->createTeam();

        $host = Host::factory()->create([
            'team_id' => $team->id,
        ]);

        resolve(DeleteHostAction::class)->execute($host);

        $this->assertSoftDeleted($host);

        Event::assertDispatched(HostDeleted::class);
    }

    /** @test */
    public function will_dispatch_job_to_clean_up_host_data_and_hard_delete_host()
    {
        Bus::fake();

        $team = $this->createTeam();

        $host = Host::factory()->create([
            'team_id' => $team->id,
        ]);

        resolve(DeleteHostAction::class)->execute($host);

        Bus::assertChained([
            PostDeleteHostTidyUp::class,
            HardDeleteHost::class,
        ]);
    }
}
