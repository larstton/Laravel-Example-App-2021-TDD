<?php

namespace Tests\Unit\Actions\ServiceCheck;

use App\Actions\ServiceCheck\UpdateServiceCheckAction;
use App\Enums\CheckLastSuccess;
use App\Events\ServiceCheck\ServiceCheckUpdated;
use App\Models\ServiceCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateServiceCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_service_check_using_supplied_data()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $serviceCheck = ServiceCheck::factory()->for($host)->create([
            'active'         => true,
            'check_interval' => 60,
            'last_success'   => CheckLastSuccess::Success(),
        ]);

        $serviceCheck = resolve(UpdateServiceCheckAction::class)->execute($serviceCheck, false, 120);

        $serviceCheck->refresh();

        $this->assertInstanceOf(ServiceCheck::class, $serviceCheck);
        $this->assertFalse($serviceCheck->active);
        $this->assertEquals(120, $serviceCheck->check_interval);
        $this->assertTrue($serviceCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertTrue($now->is($serviceCheck->updated_at));
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            ServiceCheckUpdated::class,
        ]);

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $serviceCheck = ServiceCheck::factory()->for($host)->create([
            'active'         => true,
            'check_interval' => 60,
            'last_success'   => CheckLastSuccess::Success(),
        ]);

        resolve(UpdateServiceCheckAction::class)->execute($serviceCheck, false, 120);

        Event::assertDispatched(ServiceCheckUpdated::class);
    }
}
