<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\LogHostHistoryAction;
use App\Enums\TeamPlan;
use App\Models\HostHistory;
use Illuminate\Support\Carbon;
use LogicException;
use Tests\TestCase;

class LogHostHistoryActionTest extends TestCase
{
    /** @test */
    public function will_log_host_history_on_host_create()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);

        resolve(LogHostHistoryAction::class)->execute($host, 'create');

        $this->assertDatabaseHas('host_histories', [
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
            'name'    => $host->name,
            'paid'    => false,
        ]);
    }

    /** @test */
    public function will_log_paid_host_history_on_host_create_when_team_on_payg()
    {
        $team = $this->createTeam([
            'plan' => TeamPlan::Payg(),
        ]);
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);

        resolve(LogHostHistoryAction::class)->execute($host, 'create');

        $this->assertDatabaseHas('host_histories', [
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
            'name'    => $host->name,
            'paid'    => true,
        ]);
    }

    /** @test */
    public function will_log_host_history_on_host_update()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
            'name'    => 'new_name',
        ]);
        HostHistory::factory()->create([
            'name'    => 'old_name',
            'host_id' => $host->id,
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
        ]);

        resolve(LogHostHistoryAction::class)->execute($host, 'update');

        $this->assertDatabaseHas('host_histories', [
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
            'name'    => $host->name,
        ]);
    }

    /** @test */
    public function will_log_host_history_on_host_delete()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
            'name'    => 'new_name',
        ]);
        HostHistory::factory()->create([
            'name'    => 'old_name',
            'host_id' => $host->id,
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
        ]);

        resolve(LogHostHistoryAction::class)->execute($host, 'delete');

        $this->assertDatabaseHas('host_histories', [
            'host_id'    => $host->id,
            'team_id'    => $host->team_id,
            'user_id'    => $host->user_id,
            'deleted_at' => $now,
        ]);
    }

    /** @test */
    public function will_throw_exception_if_incorrect_method_supplied()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);

        $this->expectException(LogicException::class);

        resolve(LogHostHistoryAction::class)->execute($host, 'incorrect-method');
    }
}
