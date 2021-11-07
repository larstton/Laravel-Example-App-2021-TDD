<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\CreatePaidHostHistoryOnTeamPlanUpgradeAction;
use App\Models\HostHistory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreatePaidHostHistoryOnTeamPlanUpgradeActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_duplicate_all_active_unpaid_host_histories_to_paid_for_given_team()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        HostHistory::factory()
            ->for($team)
            ->for($host)
            ->for($user, 'createdBy')
            ->count(3)
            ->state(new Sequence(
                ['name' => 'name-1'],
                ['name' => 'name-2'],
                ['name' => 'name-3'],
            ))
            ->create([
                'paid' => false,
            ]);

        resolve(CreatePaidHostHistoryOnTeamPlanUpgradeAction::class)->execute($team);

        $this->assertDatabaseHas('host_histories', [
            'host_id'    => $host->id,
            'team_id'    => $team->id,
            'user_id'    => $user->id,
            'name'       => 'name-1',
            'created_at' => $now,
            'updated_at' => $now,
            'paid'       => true,
        ]);
        $this->assertDatabaseHas('host_histories', [
            'host_id'    => $host->id,
            'team_id'    => $team->id,
            'user_id'    => $user->id,
            'name'       => 'name-2',
            'created_at' => $now,
            'updated_at' => $now,
            'paid'       => true,
        ]);
        $this->assertDatabaseHas('host_histories', [
            'host_id'    => $host->id,
            'team_id'    => $team->id,
            'user_id'    => $user->id,
            'name'       => 'name-3',
            'created_at' => $now,
            'updated_at' => $now,
            'paid'       => true,
        ]);
    }
}
