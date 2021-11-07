<?php

namespace App\Http\Middleware;

use Closure;
use function Sentry\configureScope;
use Sentry\State\Scope;

class SentryContext
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && app()->bound('sentry')) {
            configureScope(function (Scope $scope): void {
                $scope->setUser([
                    'id'    => current_user()->id,
                    'email' => current_user()->email,
                ]);
            });
        }

        return $next($request);
    }
}
