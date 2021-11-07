<?php

namespace App\Http\Controllers\Frontman;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontman\FrontmanSummaryListResource;
use App\Models\Frontman;

class FrontmanSummaryListController extends Controller
{
    public function __invoke()
    {
        $hosts = Frontman::query()
            ->private()
            ->orderBy('location')
            ->get(['id', 'location']);

        return FrontmanSummaryListResource::collection($hosts);
    }
}
