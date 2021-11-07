<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\UnsubscribeRecipientFromDailySummaryAction;
use App\Events\Recipient\RecipientUnsubscribedFromDailySummary;
use App\Events\Recipient\RecipientUpdated;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnsubscribeRecipientFromDailySummaryActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_unsubscribe_from_daily_summary_for_recipient()
    {
        $team = $this->createTeam([], false);

        $recipient = Recipient::factory()->for($team)->create([
            'daily_summary' => true,
        ]);

        $recipient = resolve(UnsubscribeRecipientFromDailySummaryAction::class)->execute($recipient);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertFalse($recipient->daily_summary);
    }

    /** @test */
    public function will_log_activity()
    {
        $team = $this->createTeam([], false);

        $recipient = Recipient::factory()->for($team)->create([
            'daily_summary' => true,
        ]);

        $recipient = resolve(UnsubscribeRecipientFromDailySummaryAction::class)->execute($recipient);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => null,
            'subject_id'   => $recipient->id,
            'subject_type' => Recipient::class,
            'description'  => sprintf("Summary for recipient \"%s\" disabled", $recipient->sendto),
        ]);
    }

    /** @test */
    public function will_dispatch_events()
    {
        Event::fake([
            RecipientUpdated::class,
            RecipientUnsubscribedFromDailySummary::class,
        ]);

        $team = $this->createTeam([], false);

        $recipient = Recipient::factory()->for($team)->create([
            'daily_summary' => true,
        ]);

        resolve(UnsubscribeRecipientFromDailySummaryAction::class)->execute($recipient);

        Event::assertDispatched(RecipientUpdated::class);
        Event::assertDispatched(RecipientUnsubscribedFromDailySummary::class);
    }
}
