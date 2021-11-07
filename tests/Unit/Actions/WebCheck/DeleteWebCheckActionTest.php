<?php

namespace Actions\WebCheck;

use App\Actions\WebCheck\DeleteWebCheckAction;
use App\Events\WebCheck\WebCheckDeleted;
use App\Jobs\WebCheck\DeleteWebCheck;
use App\Models\WebCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteWebCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_web_check()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $webCheck = WebCheck::factory()->create();

        resolve(DeleteWebCheckAction::class)->execute($user, $webCheck, $host);

        $this->assertDeleted($webCheck);
    }

    /** @test */
    public function will_dispatch_deleted_event()
    {
        Event::fake([
            WebCheckDeleted::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $webCheck = WebCheck::factory()->create();

        resolve(DeleteWebCheckAction::class)->execute($user, $webCheck, $host);

        Event::assertDispatched(WebCheckDeleted::class);
    }

    /** @test */
    public function will_dispatch_job()
    {
        Bus::fake([
            DeleteWebCheck::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $webCheck = WebCheck::factory()->create();

        resolve(DeleteWebCheckAction::class)->execute($user, $webCheck, $host);

        Bus::assertDispatched(function (DeleteWebCheck $job) use ($host, $webCheck, $user) {
            $this->assertTrue($user->is($job->user));
            $this->assertTrue($webCheck->is($job->webCheck));
            $this->assertTrue($host->is($job->host));

            return true;
        });
    }
}
