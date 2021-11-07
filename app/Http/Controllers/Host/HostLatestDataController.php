<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Resources\Host\HostLatestDataResource;
use App\Models\Host;
use App\Models\WebCheck;
use App\Support\LatestData\LatestDataRepository;

class HostLatestDataController extends Controller
{
    public function __invoke(Host $host)
    {
        return HostLatestDataResource::make(
            (new LatestDataRepository($host))->build()
        );
    }
}
