<?php

namespace App\Actions\Team;

use App\Enums\TeamStatus;
use App\Exceptions\TeamException;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\Team\TeamMemberInvitationNotification;

class SendTeamMemberInvitationAction
{
    public function execute(TeamMember $teamMember, User $user): void
    {
        throw_if(
            $teamMember->team_status->is(TeamStatus::Joined()),
            TeamException::requirementNotMet('This team member has already joined.')
        );

        $teamMember->notify(new TeamMemberInvitationNotification($user));
    }
}
