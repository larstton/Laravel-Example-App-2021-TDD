<?php

namespace App\Http\Controllers\Host;

use App\Actions\Host\FormatGroupAggregatedHostDataAction;
use App\Http\Controllers\Controller;
use App\Http\Queries\GroupAggregatedHostDataQuery;
use App\Http\Resources\Host\AggregatedHostDataResource;

class GroupAggregatedHostDataController extends Controller
{
    public function __invoke(
        GroupAggregatedHostDataQuery $query,
        FormatGroupAggregatedHostDataAction $formatTagAggregatedHostDataAction
    ) {
        return AggregatedHostDataResource::collection(
            $formatTagAggregatedHostDataAction->execute($query->jsonPaginate())
        );
    }
}
