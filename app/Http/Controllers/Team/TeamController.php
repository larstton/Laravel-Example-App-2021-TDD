<?php

namespace App\Http\Controllers\Team;

use App\Actions\Team\UpdateTeamAction;
use App\Data\Team\UpdateTeamData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\Team\TeamResource;

class TeamController extends Controller
{
    public function index()
    {
        $this->authorize('view', $team = current_team());

        return TeamResource::make($team);
    }

    public function update(UpdateTeamRequest $request, UpdateTeamAction $updateTeamAction)
    {
        $this->authorize('update', $team = current_team());

        $team = $updateTeamAction->execute($team, UpdateTeamData::fromRequest($request));

        return TeamResource::make($team);
    }
}
