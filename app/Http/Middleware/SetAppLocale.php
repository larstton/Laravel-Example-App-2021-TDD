<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SetAppLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Content-Language', config('app.locale'));

        abort_if(
            is_null(Arr::first(config('cloudradar.languages'), fn ($value) => $value === $locale)),
            403, 'Unsupported language'
        );

        app()->setLocale($locale);

        return $next($request);
    }
}
