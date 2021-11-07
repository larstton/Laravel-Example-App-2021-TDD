<?php

namespace App\Support\Influx;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InfluxGroupedByMetricCollection extends Collection
{
    /**
     * extracts individual metrics from data points returned by influx
     * i.e. ['time' =>..., 'mean_metric1'=>...,'mean_metric2'=>...] to
     * ['metric1' => ['time' =>..., 'mean_metric1'=>...], 'metric2' => ['time' =>..., 'mean_metric2'=>...]]
     *
     * @param  Collection  $results
     * @return InfluxGroupedByMetricCollection
     */
    public static function ungroupResults(Collection $results): InfluxGroupedByMetricCollection
    {
        $ungroupedResults = self::make([]);

        //this iterates over results grouped my measurement, i.e. fs.*, net.*
        $results->each(function ($pointCollection) use ($ungroupedResults) {
            //this iterates over individual data points within graph data
            $pointCollection->each(function ($dataPoint) use ($ungroupedResults, $pointCollection) {
                $dataPointCollection = collect($dataPoint);
                //iterate over one point keys and move data belonging to one measurement to ungrouped collection
                $dataPointCollection->each(function ($value, $key) use ($ungroupedResults, $dataPointCollection) {
                    $metricName = Str::of($key);
                    if ($metricName->startsWith('mean')) {
                        $keyName = $metricName->replace('mean_', '')->__toString();
                        if (is_null($ungroupedResults->get($keyName))) {
                            $ungroupedResults->put($keyName, InfluxResultCollection::make());
                        }
                        $ungroupedResults
                            ->get($keyName)
                            ->add($dataPointCollection
                                ->filter(function ($value, $key) use ($keyName) {
                                    if ('time' == $key) {
                                        return true;
                                    }

                                    return Str::of($key)->contains($keyName);
                                })
                            );
                    }
                });
            });
        });

        //quick graphs expect to have all the keys in response event if they are empty
        $filter = request()->query->get('filter');
        if (isset($filter['alias']) && 'quick-view-graph-data' === $filter['alias']) {
            $expectedMetrics = collect(AliasResolver::resolveAliasToMetricGroup($filter['alias']));
            $expectedMetrics->keys()->each(function ($metric) use ($ungroupedResults) {
                if (is_null($ungroupedResults->get($metric))) {
                    $ungroupedResults->put($metric, InfluxResultCollection::make());
                }
            });
        }

        return $ungroupedResults;
    }

    public function flattenIfOneMetric()
    {
        return $this->pipe(fn ($items) => $items->count() === 1
            ? self::make($items->first())
            : $items);
    }

    /**
     * Transforms collection from
     * ['col1' => 'net.*','col2' => 'net.*'] to ['net.*' => ['col1','col2']]
     *
     * @return InfluxGroupedByMetricCollection
     */
    public function groupByMeasurement(): InfluxGroupedByMetricCollection
    {
        $grouped = $this->reduce(function (Collection $carry, $measurement, $metric) {
            $metrics = $carry->get($measurement, collect([]));

            return $carry->put($measurement, $metrics->push($metric));

        }, collect([]));

        return self::make($grouped->toArray());
    }
}
