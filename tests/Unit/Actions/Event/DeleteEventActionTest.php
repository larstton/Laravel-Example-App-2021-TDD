<?php

namespace Tests\Unit\Actions\Event;

use App\Actions\Event\DeleteEventAction;
use App\Models\Event;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class DeleteEventActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_delete_event()
    {
        $team = $this->createTeam();
        $event = Event::factory()->for($team)->create();

        resolve(DeleteEventAction::class)->execute($event);

        $this->assertDeleted($event);
    }

    /** @test */
    public function will_recover_event_with_notifier()
    {
        $team = $this->createTeam();
        $event = Event::factory()->for($team)->create();
        $this->notifierService->shouldReceive('recoverEvent', $event)->andReturnTrue();
        resolve(DeleteEventAction::class)->execute($event);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
