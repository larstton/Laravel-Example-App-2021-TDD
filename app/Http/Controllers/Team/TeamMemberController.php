<?php

namespace App\Http\Controllers\Team;

use App\Actions\Team\CreateTeamMemberAction;
use App\Actions\Team\DeleteTeamMemberAction;
use App\Actions\Team\UpdateTeamMemberAction;
use App\Data\Team\CreateTeamMemberData;
use App\Data\Team\UpdateTeamMemberData;
use App\Http\Controllers\Controller;
use App\Http\Queries\TeamMemberQuery;
use App\Http\Requests\Team\CreateTeamMemberRequest;
use App\Http\Requests\Team\UpdateTeamMemberRequest;
use App\Http\Resources\Team\TeamMemberResource;
use App\Models\TeamMember;

class TeamMemberController extends Controller
{
    public function index(TeamMemberQuery $query)
    {
        $this->authorize(TeamMember::class);

        return TeamMemberResource::collection($query->jsonPaginate());
    }

    public function store(CreateTeamMemberRequest $request, CreateTeamMemberAction $inviteTeamMemberAction)
    {
        $this->authorize(TeamMember::class);

        $invitedTeamMember = $inviteTeamMemberAction->execute(
            $this->user(),
            CreateTeamMemberData::fromRequest($request)
        );

        return TeamMemberResource::make($invitedTeamMember);
    }

    public function show(TeamMember $teamMember)
    {
        $this->authorize($teamMember);

        return TeamMemberResource::make($teamMember);
    }

    public function update(
        UpdateTeamMemberRequest $request,
        TeamMember $teamMember,
        UpdateTeamMemberAction $updateTeamMemberAction
    ) {
        $this->authorize($teamMember);

        $teamMember = $updateTeamMemberAction->execute(
            $teamMember,
            UpdateTeamMemberData::fromRequest($request)
        );

        return TeamMemberResource::make($teamMember);
    }

    public function destroy(TeamMember $teamMember, DeleteTeamMemberAction $deleteTeamMemberAction)
    {
        $this->authorize($teamMember);

        $deleteTeamMemberAction->execute($teamMember, request('removeRecipient', false));

        return $this->noContent();
    }
}
