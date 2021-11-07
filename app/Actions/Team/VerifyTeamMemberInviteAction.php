<?php

namespace App\Actions\Team;

use App\Models\TeamMember;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyTeamMemberInviteAction
{
    public function execute(TeamMember $teamMember, string $token): TeamMember
    {
        throw_unless(
            hash_equals($token, sha1($teamMember->getEmailForVerification())),
            new AuthorizationException
        );

        $teamMember->team->makeCurrentTenant();

        if (! $teamMember->hasVerifiedEmail()) {
            $teamMember->markEmailAsVerified();
        }

        return $teamMember->refresh();
    }
}
