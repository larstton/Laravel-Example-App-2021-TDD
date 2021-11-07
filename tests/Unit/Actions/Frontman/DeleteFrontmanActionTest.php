<?php

namespace Tests\Unit\Actions\Frontman;

use App\Actions\Frontman\DeleteFrontmanAction;
use App\Enums\EventState;
use App\Events\Event\EventDeleted;
use App\Events\Frontman\FrontmanDeleted;
use App\Exceptions\FrontmanException;
use App\Models\Event;
use App\Models\Frontman;
use App\Models\Reminder;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event as EventDispatcher;
use Tests\TestCase;

class DeleteFrontmanActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_delete_frontman()
    {
        $team = $this->createTeam();
        $frontman = Frontman::factory()->for($team)->create();

        resolve(DeleteFrontmanAction::class)->execute($frontman);

        $this->assertDeleted($frontman);
        EventDispatcher::assertDispatched(FrontmanDeleted::class);
    }

    /** @test */
    public function will_recover_events_linked_to_frontman_via_notifier()
    {
        $team = $this->createTeam();
        $frontman = Frontman::factory()->for($team)->create();
        $event = Event::factory()->for($team)->create([
            'host_id' => $frontman->id,
            'state'   => EventState::Active(),
        ]);
        $this->notifierService->shouldReceive('recoverEvent', $event)->andReturnTrue();
        resolve(DeleteFrontmanAction::class)->execute($frontman);
    }

    /** @test */
    public function will_delete_sent_reminders_of_linked_events()
    {
        $team = $this->createTeam();
        $frontman = Frontman::factory()->for($team)->create();
        $event = Event::factory()->for($team)->create([
            'host_id' => $frontman->id,
            'state'   => EventState::Recovered(),
        ]);
        $reminders = Reminder::factory()->for($event)->create();
        resolve(DeleteFrontmanAction::class)->execute($frontman);

        $this->assertDeleted($reminders);
    }

    /** @test */
    public function will_delete_linked_events()
    {
        $team = $this->createTeam();
        $frontman = Frontman::factory()->for($team)->create();
        $event = Event::factory()->for($team)->create([
            'host_id' => $frontman->id,
            'state'   => EventState::Recovered(),
        ]);
        resolve(DeleteFrontmanAction::class)->execute($frontman);

        $this->assertDeleted($event);
        EventDispatcher::assertDispatched(EventDeleted::class);
    }

    /** @test */
    public function wont_delete_frontman_if_in_use()
    {
        $team = $this->createTeam();
        $frontman = Frontman::factory()->for($team)->create();
        $this->createHost([
            'team_id'     => $team->id,
            'frontman_id' => $frontman->id,
        ]);

        $this->expectException(FrontmanException::class);
        $this->expectErrorMessage('This frontman is still in use. Detach all hosts first.');
        resolve(DeleteFrontmanAction::class)->execute($frontman);

        Event::assertNotDispatched(FrontmanDeleted::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
