<?php

namespace Tests\Unit\Actions\Jobmon;

use App\Actions\Jobmon\DeleteJobmonJobAction;
use App\Enums\EventState;
use App\Events\Event\EventDeleted;
use App\Events\JobmonResult\JobmonResultDeleted;
use App\Models\Event;
use App\Models\JobmonResult;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class DeleteJobmonJobActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_delete_jobmon_from_host_and_job_id()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $jobMonResult = JobmonResult::factory()->for($host)->create([
            'job_id' => 'job-id',
        ]);

        resolve(DeleteJobmonJobAction::class)->execute($host, 'job-id');

        $this->assertDeleted($jobMonResult);
        \Illuminate\Support\Facades\Event::assertDispatched(JobmonResultDeleted::class);
    }

    /** @test */
    public function wont_delete_host_jobmon_results_with_different_job_id()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        $jobMonResult = JobmonResult::factory()->for($host)->create([
            'job_id' => 'job-id-2',
        ]);

        resolve(DeleteJobmonJobAction::class)->execute($host, 'job-id');

        $this->assertTrue($jobMonResult->refresh()->exists());
    }

    /** @test */
    public function will_recover_events_linked_to_host_and_job_id()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        JobmonResult::factory()->for($host)->create([
            'job_id' => 'job-id',
        ]);
        $event = Event::factory()->for($host)->for($team)->create([
            'check_key' => 'jobmon:job-id',
            'check_id'  => $host->id,
            'state'     => EventState::Active(),
        ]);

        $this->notifierService->shouldReceive('recoverEvent', $event)->andReturnTrue();

        resolve(DeleteJobmonJobAction::class)->execute($host, 'job-id');
    }

    /** @test */
    public function will_delete_events_linked_to_host_and_job_id()
    {
        $team = $this->createTeam();
        $host = $this->createHost($team);
        JobmonResult::factory()->for($host)->create([
            'job_id' => 'job-id',
        ]);
        $event = Event::factory()->for($host)->for($team)->create([
            'check_key' => 'jobmon:job-id',
            'check_id'  => $host->id,
            'state'     => EventState::Active(),
        ]);

        resolve(DeleteJobmonJobAction::class)->execute($host, 'job-id');

        $this->assertDeleted($event);
        \Illuminate\Support\Facades\Event::assertDispatched(EventDeleted::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
