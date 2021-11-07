<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\MuteCommentNotificationsForRecipientAction;
use App\Events\Recipient\RecipientUpdated;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MuteCommentNotificationsForRecipientActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_mute_comments_on_passed_recipient()
    {
        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'comments' => true,
        ]);

        $recipient = resolve(MuteCommentNotificationsForRecipientAction::class)->execute($recipient);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertFalse($recipient->comments);
    }

    /** @test */
    public function will_log_activity()
    {
        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'comments' => true,
        ]);

        $recipient = resolve(MuteCommentNotificationsForRecipientAction::class)->execute($recipient);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => null,
            'subject_id'   => $recipient->id,
            'subject_type' => Recipient::class,
            'description'  => sprintf("Muted comment notifications for %s %s", $recipient->media_type,
                $recipient->sendto),
        ]);
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            RecipientUpdated::class,
        ]);

        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'comments' => true,
        ]);

        resolve(MuteCommentNotificationsForRecipientAction::class)->execute($recipient);

        Event::assertDispatched(RecipientUpdated::class);
    }
}
