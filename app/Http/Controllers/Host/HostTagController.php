<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host;

class HostTagController extends Controller
{
    public function __invoke()
    {
        return $this->json([
            'data' => Host::getTagListForActiveHosts()
                ->pluck('name')
                ->all(),
        ]);
    }
}
