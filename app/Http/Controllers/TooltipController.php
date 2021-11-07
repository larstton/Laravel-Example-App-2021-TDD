<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TooltipController extends Controller
{
    public function __invoke(Request $request, $file)
    {
        $path = resource_path(
            sprintf('/lang/%s/tooltips/%s.html', $request->input('locale', 'en'), trim($file))
        );

        $file = Cache::remember($key = "tooltip-{$path}", now()->addHours(6), function () use ($path) {
            if (File::exists($path)) {
                return File::get($path);
            }

            return null;
        });

        if (! is_null($file)) {
            return $file;
        }

        Cache::forget($key);
        $this->errorNotFound();
    }
}
