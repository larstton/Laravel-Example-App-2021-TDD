<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\NotifyEventSubscribersOfNewCommentAction;
use App\Data\Event\NotifyOnEventCommentData;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\Recipient;
use App\Notifications\NewCommentAddedRecipientNotification;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyEventSubscribersOfNewCommentActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_send_notification_of_new_comment_to_subscribed_recipients()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create();

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertSentTo(
            $recipient,
            NewCommentAddedRecipientNotification::class,
            function ($notification, $channels, $notifiable, $locale) {
                return in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function wont_send_to_recipients_who_have_disabled_comment_notifications()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);
        $recipient = Recipient::factory()->for($team)->create([
            'comments' => false,
        ]);

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertNotSentTo($recipient, NewCommentAddedRecipientNotification::class);
    }

    /** @test */
    public function wont_send_to_recipients_who_are_not_verified()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create([
            'verified' => false,
        ]);

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertNotSentTo($recipient, NewCommentAddedRecipientNotification::class);
    }

    /** @test */
    public function wont_send_to_recipients_who_are_not_active()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create([
            'active' => false,
        ]);

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertNotSentTo($recipient, NewCommentAddedRecipientNotification::class);
    }

    /** @test */
    public function wont_send_if_event_comment_forward_flag_set_to_false()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => false,
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create();

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertNotSentTo($recipient, NewCommentAddedRecipientNotification::class);
    }

    /** @test */
    public function will_ping_notifier_for_each_notified_recipient()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => false,
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create();

        $payload = new NotifyOnEventCommentData([
            'nickname'  => $eventComment->user->nickname,
            'timestamp' => now()->unix(),
            'timezone'  => $eventComment->user->team->timezone,
            'text'      => $eventComment->text,
            'eventUuid' => $eventComment->event_id,
        ]);
        $payload->addRecipient($recipient);
        $this->notifierService
            ->shouldReceive('sendEventComment', $payload)
            ->andReturnTrue();

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);
    }

    /** @test */
    public function wont_ping_notifier_if_recipient_not_subscribed_to_comments()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);
        Recipient::factory()->for($team)->create([
            'comments' => false,
        ]);

        $this->notifierService->shouldNotHaveReceived('sendEventComment');

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);
    }

    /** @test */
    public function wont_send_comment_to_recipients_who_are_on_another_team()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'other',
        ]);
        $otherTeamRecipient = Recipient::factory()
            ->for($team)->for($user)
            ->subscribedToComments()
            ->create();

        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'lee',
        ]);
        $recipient = Recipient::factory()->for($team)->subscribedToComments()->create();

        $event = Event::factory()->for($team)->create();
        $eventComment = EventComment::factory()->for($team)->for($user)->for($event)->create([
            'forward' => true,
        ]);

        resolve(NotifyEventSubscribersOfNewCommentAction::class)->execute($eventComment);

        Notification::assertSentTo($recipient, NewCommentAddedRecipientNotification::class);
        Notification::assertNotSentTo($otherTeamRecipient, NewCommentAddedRecipientNotification::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
