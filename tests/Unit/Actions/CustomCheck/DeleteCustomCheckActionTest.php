<?php

namespace Tests\Unit\Actions\CustomCheck;

use App\Actions\CustomCheck\DeleteCustomCheckAction;
use App\Jobs\CustomCheck\DeleteCustomCheck;
use App\Models\CustomCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DeleteCustomCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_custom_check()
    {
        Bus::fake([
            DeleteCustomCheck::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $customCheck = CustomCheck::factory()->for($host)->create();

        resolve(DeleteCustomCheckAction::class)->execute($user, $customCheck, $host);

        $this->assertDeleted($customCheck);
    }

    /** @test */
    public function will_dispatch_job_to_do_tidy_up_after_deleting_check()
    {
        Bus::fake([
            DeleteCustomCheck::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $customCheck = CustomCheck::factory()->for($host)->create();

        resolve(DeleteCustomCheckAction::class)->execute($user, $customCheck, $host);

        Bus::assertDispatched(function (DeleteCustomCheck $job) use ($host, $user, $customCheck) {
            return $job->user->id === $user->id
                && $job->customCheck->id === $customCheck->id
                && $job->host->id === $host->id
                && $job->isLastCheck;
        });
    }

    /** @test */
    public function will_pass_false_to_job_if_not_last_custom_check()
    {
        Bus::fake([
            DeleteCustomCheck::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        CustomCheck::factory()->for($host)->create();
        $customCheck = CustomCheck::factory()->for($host)->create();

        resolve(DeleteCustomCheckAction::class)->execute($user, $customCheck, $host);

        Bus::assertDispatched(function (DeleteCustomCheck $job) {
            return $job->isLastCheck === false;
        });
    }
}
