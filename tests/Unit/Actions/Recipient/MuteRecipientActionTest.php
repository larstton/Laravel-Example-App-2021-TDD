<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\MuteRecipientAction;
use App\Events\Recipient\RecipientUpdated;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MuteRecipientActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_mute_alerts_warnings_and_reminders_for_passed_recipient()
    {
        $team = $this->createTeam([], false);
        $recipient = Recipient::factory()->for($team)->create([
            'alerts'    => true,
            'warnings'  => true,
            'reminders' => true,
        ]);

        $recipient = resolve(MuteRecipientAction::class)->execute($recipient);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertFalse($recipient->alerts);
        $this->assertFalse($recipient->warnings);
        $this->assertFalse($recipient->reminders);
    }

    /** @test */
    public function will_log_activity()
    {
        $team = $this->createTeam([], false);
        $recipient = Recipient::factory()->for($team)->create([
            'alerts'    => true,
            'warnings'  => true,
            'reminders' => true,
        ]);

        $recipient = resolve(MuteRecipientAction::class)->execute($recipient);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => null,
            'subject_id'   => $recipient->id,
            'subject_type' => Recipient::class,
            'description'  => sprintf("Disabled alerting for %s %s", $recipient->media_type, $recipient->sendto),
        ]);
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            RecipientUpdated::class,
        ]);

        $team = $this->createTeam([], false);
        $recipient = Recipient::factory()->for($team)->create([
            'alerts'    => true,
            'warnings'  => true,
            'reminders' => true,
        ]);

        resolve(MuteRecipientAction::class)->execute($recipient);

        Event::assertDispatched(RecipientUpdated::class);
    }
}
