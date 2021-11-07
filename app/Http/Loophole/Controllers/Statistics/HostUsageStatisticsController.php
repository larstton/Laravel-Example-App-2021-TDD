<?php

namespace App\Http\Loophole\Controllers\Statistics;

use App\Actions\HostHistory\BuildHostHistoryUsageStatisticsAction;
use App\Http\Controllers\Controller;
use App\Http\Loophole\Requests\HostUsageStatisticsRequest;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Carbon\Carbon;

class HostUsageStatisticsController extends Controller
{
    public function __invoke(
        HostUsageStatisticsRequest $request,
        Team $team,
        BuildHostHistoryUsageStatisticsAction $action
    ) {
        TenantManager::enableTenancyChecks();
        TenantManager::setCurrentTenant($team);

        // IMPORTANT
        // This controller is used by the checkout service, so response structure must be kept.
        return $this->json([
            'success' => true,
            'usage'   => $action->execute(Carbon::parse($request->period ?? date('Y-m'))),
        ]);
    }
}
