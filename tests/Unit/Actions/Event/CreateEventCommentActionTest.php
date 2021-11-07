<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\CreateEventCommentAction;
use App\Data\Event\EventCommentData;
use App\Events\Event\EventCommentCreated;
use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Support\Facades\Event as EventDispatcher;
use Tests\TestCase;

class CreateEventCommentActionTest extends TestCase
{
    /** @test */
    public function will_create_event_comment_for_given_event()
    {
        EventDispatcher::fake([
            EventCommentCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $data = new EventCommentData([
            'text'            => 'text...',
            'visibleToGuests' => true,
            'statuspage'      => false,
            'forward'         => true,
        ]);

        $eventComment = resolve(CreateEventCommentAction::class)->execute($user, $event, $data);

        $this->assertInstanceOf(EventComment::class, $eventComment);
        $this->assertEquals($event->id, $eventComment->event_id);
        $this->assertEquals($user->id, $eventComment->user_id);
        $this->assertEquals($team->id, $eventComment->team_id);
        $this->assertEquals('text...', $eventComment->text);
        $this->assertEquals('lee', $eventComment->nickname);
        $this->assertEquals(true, $eventComment->visible_to_guests);
        $this->assertEquals(false, $eventComment->statuspage);
        $this->assertEquals(true, $eventComment->forward);

        EventDispatcher::assertDispatched(EventCommentCreated::class);
    }
}
