<?php

namespace App\Http\Controllers\Team;

use App\Actions\Team\SendTeamMemberInvitationAction;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Gate;

class ResendTeamInvitationController extends Controller
{
    public function __invoke(TeamMember $teamMember, SendTeamMemberInvitationAction $invitationAction)
    {
        Gate::authorize('role-team-admin');

        $invitationAction->execute($teamMember, $this->user());

        return $this->accepted();
    }
}
