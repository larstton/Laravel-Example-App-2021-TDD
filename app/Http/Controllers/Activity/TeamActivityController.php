<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Http\Queries\TeamActivityQuery;
use App\Http\Requests\Activity\TeamActivityRequest;
use App\Http\Resources\Activity\TeamActivityResource;

class TeamActivityController extends Controller
{
    public function __invoke(TeamActivityRequest $request, TeamActivityQuery $query)
    {
        return TeamActivityResource::collection($query->jsonPaginate());
    }
}
