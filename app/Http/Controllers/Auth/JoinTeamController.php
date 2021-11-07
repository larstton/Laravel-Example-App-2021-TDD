<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CompleteRegistrationForTeamMemberAction;
use App\Data\Auth\TeamMemberRegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TeamMemberRegisterRequest;
use App\Models\TeamMember;
use Illuminate\Auth\Access\AuthorizationException;

class JoinTeamController extends Controller
{
    public function __invoke(
        TeamMemberRegisterRequest $request,
        CompleteRegistrationForTeamMemberAction $completeRegistrationForTeamMemberAction
    ) {
        $teamMember = TeamMember::withoutTeamScope()
            ->where('email', $request->email)
            ->where('id', $request->id)
            ->first();

        throw_unless(
            ! is_null($teamMember)
            && hash_equals($teamMember->makeVerificationSignature(), $request->signature),
            new AuthorizationException
        );

        auth()->setUser($teamMember);

        $completeRegistrationForTeamMemberAction->execute(
            $teamMember,
            TeamMemberRegisterData::fromTeamMemberRegisterRequest($request)
        );

        return $this->created();
    }
}
