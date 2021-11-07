<?php

namespace Tests\Unit\Actions\ServiceCheck;

use App\Actions\ServiceCheck\DeleteServiceCheckAction;
use App\Events\ServiceCheck\ServiceCheckDeleted;
use App\Jobs\ServiceCheck\DeleteServiceCheck;
use App\Models\ServiceCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteServiceCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_service_check()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $serviceCheck = ServiceCheck::factory()->create();

        resolve(DeleteServiceCheckAction::class)->execute($user, $serviceCheck, $host);

        $this->assertDeleted($serviceCheck);
    }

    /** @test */
    public function will_dispatch_deleted_event()
    {
        Event::fake([
            ServiceCheckDeleted::class
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $serviceCheck = ServiceCheck::factory()->create();

        resolve(DeleteServiceCheckAction::class)->execute($user, $serviceCheck, $host);

        Event::assertDispatched(ServiceCheckDeleted::class);
    }

    /** @test */
    public function will_dispatch_job()
    {
        Bus::fake([
            DeleteServiceCheck::class
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $serviceCheck = ServiceCheck::factory()->create();

        resolve(DeleteServiceCheckAction::class)->execute($user, $serviceCheck, $host);

        Bus::assertDispatched(function(DeleteServiceCheck $job) use ($host, $serviceCheck, $user) {
            $this->assertTrue($user->is($job->user));
            $this->assertTrue($serviceCheck->is($job->serviceCheck));
            $this->assertTrue($host->is($job->host));

            return true;
        });
    }
}
