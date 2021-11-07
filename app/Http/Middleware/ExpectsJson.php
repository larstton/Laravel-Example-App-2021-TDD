<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpectsJson
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        // $checkTypes = [
        //     'text/plain', 'application/json',
        // ];
        // if (Str::contains($request->header('Content-Type'), $checkTypes)) {
        //     $request->headers->set('Content-Type', 'application/json');
        // } else {
        //     $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
        // }


        $checkTypes = [
            'text/plain',
            'application/json',
        ];

        if (Str::contains($request->header('Content-Type'), $checkTypes)) {
            $request->headers->set('Content-Type', 'application/json');
        } else {
            $isJson = false;
            $body = trim($request->getContent());

            // @see https://tracker.cloudradar.info/issue/DEV-2031
            // Check if body is valid json and adjust content-type if it is.
            if (Str::startsWith($body, "{")) {
                $json = json_decode($body);
                if (! is_null($json)) {
                    $isJson = true;
                    $request->headers->set('Content-Type', 'application/json');
                }
            }

            if (! $isJson) {
                $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
            }
        }

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $next($request);
    }
}
