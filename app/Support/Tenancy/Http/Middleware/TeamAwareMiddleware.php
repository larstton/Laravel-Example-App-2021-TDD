<?php

namespace App\Support\Tenancy\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Closure;

class TeamAwareMiddleware
{
    public function handle($request, Closure $next)
    {
        // If we have an authenticated User / ApiToken for this request, then get the team, and
        // set it as the currently active team for the purposes of setting the tenancy for the
        // user. This will ensure that the scopes are set for any database queries performed.
        if (! is_null($authenticatable = $request->user())) {
            /** @var User|ApiToken $authenticatable */
            $team = $authenticatable->team;
            $team->makeCurrentTenant();
        }

        return $next($request);
    }
}
