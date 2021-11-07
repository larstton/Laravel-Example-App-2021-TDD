<?php

namespace App\Support\Influx;

use Illuminate\Support\Collection;

class InfluxGroupedByHostCollection extends Collection
{
    public function transformToGraphData(InfluxChartDataTransformer $transformer): self
    {
        return tap($this)
            ->transform(fn (InfluxGroupedByMetricCollection $results) => $results->map(
                fn (InfluxResultCollection $result) => $transformer->transform($result)
            ));
    }

    public function flattenIfOneHost(): self
    {
        return $this->pipe(fn ($items) => $items->count() === 1
            ? self::make($items->first())
            : $items);
    }
}
