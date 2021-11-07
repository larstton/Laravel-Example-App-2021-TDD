<?php

namespace Actions\Team;

use App\Actions\Team\NotifyTeamAdminTrialHasExpiredAction;
use App\Enums\TeamMemberRole;
use App\Models\TeamMember;
use App\Notifications\Team\TrialExpiredDowngradedNotification;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyTeamAdminTrialHasExpiredActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_notify_team_admins_of_team_plan_downgrade()
    {
        Notification::fake();

        $team = $this->createTeam();
        TeamMember::factory()->for($team)->count(2)->create([
            'role' => TeamMemberRole::Admin(),
        ]);

        resolve(NotifyTeamAdminTrialHasExpiredAction::class)->execute($team);

        Notification::assertSentTo($team->admins, TrialExpiredDowngradedNotification::class);
    }
}
