<?php

namespace Actions\Team;

use App\Actions\Team\SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction;
use App\Models\HostHistory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SoftDeletePaidHostHistoryOnTeamPlanDowngradeActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_teams_paid_host_history()
    {
        $team = $this->createTeam();

        $hostHistories = HostHistory::factory()->for($team)->count(2)->create([
            'paid' => true,
        ]);

        resolve(SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction::class)->execute($team);

        $hostHistories->each(function(HostHistory $hostHistory) {
            $this->assertSoftDeleted($hostHistory);
        });
    }

    /** @test */
    public function wont_delete_teams_not_paid_host_history()
    {
        $team = $this->createTeam();

        $hostHistories = HostHistory::factory()->for($team)->count(2)->create([
            'paid' => false,
        ]);

        resolve(SoftDeletePaidHostHistoryOnTeamPlanDowngradeAction::class)->execute($team);

        $hostHistories->each(function(HostHistory $hostHistory) {
            $this->assertTrue($hostHistory->refresh()->exists);
        });
    }
}
