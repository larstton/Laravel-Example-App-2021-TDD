<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Resources\Host\HostSummaryListResource;
use App\Models\Host;

class HostSummaryListController extends Controller
{
    public function __invoke()
    {
        $hosts = Host::query()
            ->whereScopedByUserHostTag(current_user())
            ->whereScopedByUserSubUnit(current_user())
            ->orderBy('name')
            ->get(['id', 'name', 'connect']);

        return HostSummaryListResource::collection($hosts);
    }
}
