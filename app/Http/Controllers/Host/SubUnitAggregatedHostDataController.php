<?php

namespace App\Http\Controllers\Host;

use App\Actions\Host\FormatSubUnitAggregatedHostDataAction;
use App\Http\Controllers\Controller;
use App\Http\Queries\SubUnitAggregatedHostDataQuery;
use App\Http\Resources\Host\AggregatedHostDataResource;

class SubUnitAggregatedHostDataController extends Controller
{
    public function __invoke(
        SubUnitAggregatedHostDataQuery $query,
        FormatSubUnitAggregatedHostDataAction $formatSubUnitAggregatedHostDataAction
    ) {
        return AggregatedHostDataResource::collection(
            $formatSubUnitAggregatedHostDataAction->execute($query->jsonPaginate())
        );
    }
}
