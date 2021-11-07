<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\UpdateEventAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Events\Event\EventUpdated;
use App\Models\Event;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateEventActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_update_event()
    {
        $team = $this->createTeam();
        /** @var Event $event */
        $event = Event::factory()->for($team)->create([
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        resolve(UpdateEventAction::class)->execute(
            $event, EventReminder::Disabled(), EventState::Recovered()
        );

        $this->assertTrue($event->state->is(EventState::Recovered()));
        $this->assertTrue($event->reminders->is(EventReminder::Disabled()));
        \Illuminate\Support\Facades\Event::assertDispatched(EventUpdated::class);
    }

    /** @test */
    public function will_ping_notifier_to_delete_reminders_for_event_when_reminders_enabled()
    {
        $team = $this->createTeam();
        /** @var Event $event */
        $event = Event::factory()->for($team)->create([
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        $this->notifierService
            ->shouldReceive('deleteRemindersForEvent', $event)
            ->andReturnTrue();

        resolve(UpdateEventAction::class)->execute(
            $event, EventReminder::Enabled(), EventState::Active()
        );
    }

    /** @test */
    public function wont_ping_notifier_to_delete_reminders_for_event_when_reminders_disabled()
    {
        $team = $this->createTeam();
        /** @var Event $event */
        $event = Event::factory()->for($team)->create([
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        $this->notifierService->shouldNotHaveBeenCalled();

        resolve(UpdateEventAction::class)->execute(
            $event, EventReminder::Disabled(), EventState::Active()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
