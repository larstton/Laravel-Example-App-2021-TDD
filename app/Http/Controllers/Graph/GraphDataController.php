<?php

namespace App\Http\Controllers\Graph;

use App\Actions\Graph\BuildGraphDataByQueryBuilderAction;
use App\Http\Controllers\Controller;
use App\Http\Queries\InfluxQuery;

class GraphDataController extends Controller
{
    public function __invoke(
        InfluxQuery $influxQuery,
        BuildGraphDataByQueryBuilderAction $graphDataByQueryBuilder
    ) {
        $data = $graphDataByQueryBuilder->execute($influxQuery);

        return $this->json(compact('data'));
    }
}
