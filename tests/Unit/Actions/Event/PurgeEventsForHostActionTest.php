<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\PurgeEventsForHostAction;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\Reminder;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PurgeEventsForHostActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_comments_of_event_of_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $event = Event::factory()->for($team)->for($host)->create();
        $eventComment = EventComment::factory()->for($team)->for($event)->create();

        resolve(PurgeEventsForHostAction::class)->execute($team, $host->id);

        $this->assertDeleted($eventComment);
    }

    /** @test */
    public function will_delete_sent_reminders_of_event_of_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $event = Event::factory()->for($team)->for($host)->create();
        $reminder = Reminder::factory()->for($event)->create();

        resolve(PurgeEventsForHostAction::class)->execute($team, $host->id);

        $this->assertDeleted($reminder);
    }

    /** @test */
    public function will_force_delete_events_of_supplied_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $event = Event::factory()->for($team)->for($host)->create();

        resolve(PurgeEventsForHostAction::class)->execute($team, $host->id);

        $this->assertDeleted($event);
    }

    /** @test */
    public function will_flush_report_cache_for_team_if_events_to_delete()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $event = Event::factory()->for($team)->for($host)->create();
        EventComment::factory()->for($team)->for($event)->create();

        Cache::shouldReceive('tags')
            ->once()
            ->with($team->getReportCacheTag())
            ->andReturnSelf()
            ->shouldReceive('flush')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        resolve(PurgeEventsForHostAction::class)->execute($team, $host->id);
    }

    /** @test */
    public function wont_flush_report_cache_for_team_if_no_events_were_deleted()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);

        Cache::spy();

        resolve(PurgeEventsForHostAction::class)->execute($team, $host->id);

        Cache::shouldNotHaveReceived('tags');
    }
}
