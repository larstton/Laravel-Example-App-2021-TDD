<?php

namespace App\Http\Klick\Controllers\TeamMember;

use App\Actions\Team\VerifyTeamMemberInviteAction;
use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;

class TeamMemberInviteVerificationController extends Controller
{
    public function __invoke(
        VerifyTeamMemberInviteAction $verifyTeamMemberInviteAction,
        TeamMember $teamMember,
        string $token
    ) {
        $teamMember = $verifyTeamMemberInviteAction->execute($teamMember, (string) $token);

        if ($teamMember->team_status->is(TeamStatus::Joined())) {
            return redirect(route('web.login'));
        }

        return redirect(route('web.team.join', [
            'email'     => $teamMember->email,
            'id'        => $teamMember->id,
            'signature' => $teamMember->makeVerificationSignature(),
        ]));
    }
}
