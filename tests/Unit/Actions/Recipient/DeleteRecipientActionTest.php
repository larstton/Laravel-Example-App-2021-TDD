<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\DeleteRecipientAction;
use App\Events\Recipient\RecipientDeleted;
use App\Models\Recipient;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteRecipientActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_delete_recipient()
    {
        $team = $this->createTeam();

        $recipient = Recipient::factory()->for($team)->create();

        resolve(DeleteRecipientAction::class)->execute($recipient);

        $this->assertDeleted($recipient);
    }

    /** @test */
    public function will_ping_notifier_service()
    {
        $team = $this->createTeam();

        $recipient = Recipient::factory()->for($team)->create();

        $this->notifierService
            ->shouldReceive('deleteRecipient', $recipient)
            ->andReturnTrue();

        resolve(DeleteRecipientAction::class)->execute($recipient);
    }

    /** @test */
    public function will_dispatch_a_deleted_event()
    {
        Event::fake([
            RecipientDeleted::class,
        ]);

        $team = $this->createTeam();

        $recipient = Recipient::factory()->for($team)->create();

        resolve(DeleteRecipientAction::class)->execute($recipient);

        Event::assertDispatched(RecipientDeleted::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
