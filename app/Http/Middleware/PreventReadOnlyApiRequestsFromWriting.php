<?php

namespace App\Http\Middleware;

use App\Enums\ApiTokenCapability;
use Closure;
use Illuminate\Http\Request;

class PreventReadOnlyApiRequestsFromWriting
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() !== 'GET' and auth()->user()->capability->isNot(ApiTokenCapability::RW())) {
            return response()->json([
                'success' => false,
                'error'   => 'Authentication failed',
                'details' => 'This is a read-only token',
            ], 403);
        }

        return $next($request);
    }
}
