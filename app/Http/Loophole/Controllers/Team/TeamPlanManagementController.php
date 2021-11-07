<?php

namespace App\Http\Loophole\Controllers\Team;

use App\Actions\Team\UpdateTeamPlanAction;
use App\Data\Team\TeamManagementUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Loophole\Requests\TeamManagementRequest;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;

class TeamPlanManagementController extends Controller
{
    public function __invoke(
        Team $team,
        TeamManagementRequest $request,
        UpdateTeamPlanAction $updateTeamPlanAction
    ) {
        TenantManager::enableTenancyChecks();
        TenantManager::setCurrentTenant($team);

        $updateTeamPlanAction->execute($team, TeamManagementUpdateData::fromRequest($request));

        return $this->accepted();
    }
}
