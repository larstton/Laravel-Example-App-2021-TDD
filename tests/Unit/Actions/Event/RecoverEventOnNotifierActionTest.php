<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\RecoverEventOnNotifierAction;
use App\Enums\EventState;
use App\Models\Event;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Mockery\MockInterface;
use Tests\TestCase;

class RecoverEventOnNotifierActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_ping_notifier_to_recover_event_when_active()
    {
        $team = $this->createTeam();
        $event = Event::factory()->for($team)->create([
            'state' => EventState::Active(),
        ]);

        $this->mock(NotifierService::class, function (MockInterface $mock) use ($event) {
            $mock->shouldReceive('recoverEvent', $event)
                ->andReturnTrue();
        });

        resolve(RecoverEventOnNotifierAction::class)->execute($event);
    }

    /** @test */
    public function wont_ping_notifier_to_recover_event_when_not_active()
    {
        $team = $this->createTeam();
        $event = Event::factory()->for($team)->create([
            'state' => EventState::Recovered(),
        ]);

        $this->mock(NotifierService::class)->shouldNotHaveBeenCalled();

        resolve(RecoverEventOnNotifierAction::class)->execute($event);
    }
}
