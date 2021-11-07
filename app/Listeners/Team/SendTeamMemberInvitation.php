<?php

namespace App\Listeners\Team;

use App\Actions\Team\SendTeamMemberInvitationAction;
use App\Events\Team\TeamMemberInvited;

class SendTeamMemberInvitation
{
    private SendTeamMemberInvitationAction $sendTeamMemberInvitationAction;

    public function __construct(SendTeamMemberInvitationAction $sendTeamMemberInvitationAction)
    {
        $this->sendTeamMemberInvitationAction = $sendTeamMemberInvitationAction;
    }

    public function handle(TeamMemberInvited $event)
    {
        $this->sendTeamMemberInvitationAction->execute($event->teamMember, $event->user);
    }
}
