<?php

namespace App\Http\Loophole\Controllers\Activity;

use App\Actions\Team\UpdateTeamPlanAction;
use App\Http\Controllers\Controller;
use App\Http\Loophole\Requests\ActivityLogRequest;
use App\Models\Team;
use App\Models\User;
use App\Support\Tenancy\Facades\TenantManager;

class ActivityLogController extends Controller
{
    public function __invoke(
        ActivityLogRequest $request,
        UpdateTeamPlanAction $updateTeamPlanAction
    ) {
        TenantManager::enableTenancyChecks();
        TenantManager::setCurrentTenant($team = Team::findOrFail($request->team));

        $user = User::findOrFail($request->user);

        activity()
            ->causedBy($user)
            ->on($team)
            ->log($request->action);

        return $this->accepted();
    }
}
