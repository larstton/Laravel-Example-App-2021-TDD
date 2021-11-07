<?php

namespace Actions\Team;

use App\Actions\Team\SendTeamMemberInvitationAction;
use App\Enums\TeamStatus;
use App\Exceptions\TeamException;
use App\Models\TeamMember;
use App\Notifications\Team\TeamMemberInvitationNotification;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendTeamMemberInvitationActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_send_team_member_invitation_notification()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $teamMember = TeamMember::factory()->for($team)->create([
            'team_status' => TeamStatus::Invited(),
        ]);

        resolve(SendTeamMemberInvitationAction::class)->execute($teamMember, $user);

        Notification::assertSentTo($teamMember, TeamMemberInvitationNotification::class);
    }

    /** @test */
    public function will_throw_exception_if_trying_to_invite_existing_member()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $teamMember = TeamMember::factory()->for($team)->create([
            'team_status' => TeamStatus::Joined(),
        ]);

        $this->expectException(TeamException::class);
        $this->expectExceptionMessage('This team member has already joined.');
        
        resolve(SendTeamMemberInvitationAction::class)->execute($teamMember, $user);

        Notification::assertNotSentTo($teamMember, TeamMemberInvitationNotification::class);
    }
}
