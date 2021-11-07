<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\CancelRemindersForEventAction;
use App\Enums\EventReminder;
use App\Models\Event;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class CancelRemindersForEventActionTest extends TestCase
{
    use WithoutEvents, WithoutTenancyChecks;

    /** @test */
    public function will_disable_reminders_for_given_event()
    {
        $team = $this->createTeam([], false);
        $event = Event::factory()->for($team)->create([
            'reminders' => EventReminder::Enabled(),
        ]);
        $recipient = Recipient::factory()->for($team)->create();

        $event = resolve(CancelRemindersForEventAction::class)->execute($event, $recipient);

        $recipient->refresh();

        $this->assertTrue($team->isCurrentTenant());
        $this->assertInstanceOf(Event::class, $event);
        $this->assertTrue($event->reminders->is(EventReminder::Disabled()));
    }

    /** @test */
    public function will_log_activity()
    {
        $team = $this->createTeam([], false);
        $event = Event::factory()->for($team)->create([
            'reminders' => EventReminder::Enabled(),
        ]);
        $recipient = Recipient::factory()->for($team)->create();

        resolve(CancelRemindersForEventAction::class)->execute($event, $recipient);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => null,
            'subject_id'   => $event->id,
            'subject_type' => Event::class,
            'description'  => sprintf("Reminder for event \"%s\" disabled", $event->meta['name']),
        ]);
    }
}
