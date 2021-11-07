<?php

namespace App\Actions\Graph;

use App\Http\Queries\InfluxQuery;
use App\Support\Influx\Facades\Influx;
use App\Support\Influx\InfluxChartDataTransformer;
use App\Support\Influx\InfluxGroupedByMetricCollection;

class BuildGraphDataByQueryBuilderAction
{
    private InfluxChartDataTransformer $transformer;

    public function __construct(InfluxChartDataTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function execute(InfluxQuery $influxQuery): array
    {
        return Influx::fetchByQueryBuilder($influxQuery)
            ->transformToGraphData($this->transformer)
            ->map(fn (InfluxGroupedByMetricCollection $results) => $results->flattenIfOneMetric())
            ->flattenIfOneHost()
            ->toArray();
    }
}
