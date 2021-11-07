<?php

namespace App\Http\Controllers\RefData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TimezonesController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->success([
            'data' => json_decode(File::get(storage_path('site/timezones.minified.json'))),
        ]);
    }
}
