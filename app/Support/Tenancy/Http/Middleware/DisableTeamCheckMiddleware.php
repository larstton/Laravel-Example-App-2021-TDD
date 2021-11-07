<?php

namespace App\Support\Tenancy\Http\Middleware;

use App\Support\Tenancy\Facades\TenantManager;
use Closure;

class DisableTeamCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        TenantManager::disableTenancyChecks();

        return $next($request);
    }
}
