<?php

namespace App\Http\Loophole\Middleware;

use Closure;

class AuthBasic
{
    public function handle($request, Closure $next)
    {
        $user = config('cloudradar.loophole.username');
        $password = config('cloudradar.loophole.password');

        $providedUsername = $request->server('PHP_AUTH_USER');
        $providedPassword = $request->server('PHP_AUTH_PW');

        if (blank($providedUsername) || blank($providedPassword)) {
            return response('Unauthenticated', 401);
        }

        if ($user != $providedUsername || $password != $providedPassword) {
            return response('Unauthenticated', 401);
        }

        return $next($request);
    }
}
