<?php

namespace App\Support\Influx;

use App\Models\Host;
use App\Support\Influx\Exceptions\InvalidFilterQuery;
use App\Support\Influx\Exceptions\InvalidSortQuery;
use App\Support\Influx\Facades\Influx;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InfluxDB\Query\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Exceptions\InvalidAppendQuery;

class InfluxQueryBuilder
{
    protected InfluxRepository $influxRepository;
    protected InfluxQueryBuilderRequest $request;
    protected ?Host $host = null;
    protected Collection $allowedFilters;
    protected Collection $setFilters;
    protected Collection $allowedSorts;
    protected Collection $setSorts;
    protected Collection $allowedAppends;
    protected Collection $setAppends;

    public function __construct(?Request $request = null, ?InfluxRepository $influxRepository = null)
    {
        $this->setFilters = collect();
        $this->setSorts = collect();
        $this->influxRepository = $influxRepository ?? resolve(InfluxRepository::class);
        $this->request = $request
            ? InfluxQueryBuilderRequest::fromRequest($request)
            : resolve(InfluxQueryBuilderRequest::class);
    }

    /**
     * Builds query via request filters defined through influx request query builder.
     *
     * @return InfluxGroupedByHostCollection
     */
    public function build(): InfluxGroupedByHostCollection
    {
        $sorts = $this->setSorts;
        $filters = $this->buildFiltersFromRequest();

        $alias = optional($filters->pull('alias'))['value'];
        $metrics = $filters->pull('metric');
        $database = optional($filters->pull('database'))['value'];
        $limit = optional($filters->pull('limit'))['value'];
        $hostFilters = $filters->pull('host');

        $database = $this->resolveDatabase($database);

        $output = InfluxGroupedByHostCollection::make();
        foreach ($hostFilters as $hostFilter) {
            $this->forHost(Host::findOrFail($hostFilter['value']));
            $filters['host'] = $hostFilter;

            $output[$this->host->id] = $this->resolveMetricsToGroup($alias, $metrics, $database)
                ->groupByMeasurement()
                ->map(function ($metrics, $measurement) use ($sorts, $limit, $filters, $database) {
                    $query = Influx::buildQueryOnDatabase($database)
                        ->from($measurement)
                        ->where($filters->pluck('where')->toArray());

                    if (! is_null($limit)) {
                        $query->limit($limit);
                    }

                    $sorts->each(function ($sort) use ($query) {
                        $query->orderBy($sort['column'], $sort['direction']);
                    });

                    return $this->buildSelectFromFilters($query, $metrics, $filters);
                });
        }


        return $output;
    }

    private function buildFiltersFromRequest(): Collection
    {
        return $this->setFilters->map(function ($value, $filter) {
            if ($filter === 'from') {
                return [
                    'value' => $value,
                    'where' => "time >= {$value}s",
                ];
            }

            if ($filter === 'to') {
                return [
                    'value' => $value,
                    'where' => "time <= {$value}s",
                ];
            }

            if ($filter === 'host') {
                return collect(Arr::wrap($value))->map(function ($uuid) {
                    return [
                        'value' => $uuid,
                        'where' => "host = '{$uuid}'",
                    ];
                })->all();
            }

            if ($filter === 'metric') {
                return collect(Arr::wrap($value))->map(fn ($metric) => ['value' => $metric])->all();
            }

            return [
                'value' => $value,
            ];
        });
    }

    private function resolveDatabase($database)
    {
        $databases = $this->influxRepository->getDatabases();

        return $databases[$database] ?? $this->influxRepository->getDefaultDatabase();
    }

    public function forHost(Host $host)
    {
        $this->host = $host;

        return $this;
    }

    private function resolveMetricsToGroup($alias, $metrics, string $database): InfluxGroupedByMetricCollection
    {
        $allMetricsForHost = $this->influxRepository->getAllMonitoredMetricsForHost($this->host);
        if (filled($group = AliasResolver::resolveAliasToMetricGroup($alias))) {

            //front-end part for quick graphs expects all the metrics to be present on response, so don't filter them
            if('quick-view-graph-data' === $alias){
                return InfluxGroupedByMetricCollection::make($group);
            }

            $groupCollection = collect($group);

            $group = $groupCollection->keys()->mapWithKeys(function ($aliasKey) use ($allMetricsForHost, $groupCollection) {
                $metricMap = collect($allMetricsForHost->get($this->influxRepository->getDefaultDatabase(), []));

                return $metricMap
                    ->filter(function ($measurement, $metric) use ($aliasKey, $groupCollection) {
                        return Str::contains($metric, $aliasKey) && $groupCollection->get($aliasKey) === $measurement;
                    })
                    ->all();
            });

            return InfluxGroupedByMetricCollection::make($group);
        }

        $group = [];
        foreach ($metrics as $metric) {
            $metric = InfluxMetric::normaliseMetric($metric['value'], $allMetricsForHost, $database);
            $measurement = optional($allMetricsForHost[$database] ?? null)[$metric];
            $group[$metric] = $measurement;
        }

        $group = array_flip(array_filter(array_flip(array_filter($group))));

        return InfluxGroupedByMetricCollection::make($group);
    }

    private function buildSelectFromFilters(Builder $query, $metrics, $filters): Builder
    {
        $groupingLabel = "";

        //do not group by when text metric is present (it will be the only metric requested)
        if(collect($metrics)->filter(function($metric){
            return InfluxMetric::isTextMetric($metric);
        })->isEmpty()) {
            // calculate grouping interval to get desired number of data points for different period
            $difference = data_get($filters, 'to.value') - data_get($filters, 'from.value');
            if ($difference < 60 * 60 + 1) {
                $query->groupBy('time(1m)');
            } elseif ($difference < 12 * 60 * 60 + 1) {
                $query->groupBy('time(10m)');
            } else {
                // if period is large - then calculate group interval so total result set has ~120 points
                $groupInterval = floor($difference / 120);
                $query->groupBy("time({$groupInterval}s)");
                //show grouping intervals in steps by 0.5 hours
                $hours = round($groupInterval * 2 / 3600) / 2;
                if ($hours > 0) {
                    $groupingLabel = sprintf(" (%.1f hour average)", $hours);
                }
            }
        }

        $selects = collect($metrics)->reduce(function ($selects, $metric) use ($groupingLabel ) {
            $metric = addslashes($metric);

            if (InfluxMetric::isTextMetric($metric)) {
                $selects[] = "{$metric}";
            } else {

                $selects[] = 'mean("'.$metric.'") AS "mean_'.$metric.$groupingLabel.'"';

                if ($this->request->appends()->contains('min')) {
                    $selects[] = 'min("'.$metric.'") AS "min_'.$metric.'"';
                }
                if ($this->request->appends()->contains('max')) {
                    $selects[] = 'max("'.$metric.'") AS "max_'.$metric.'"';
                }

            }

            return $selects;
        }, []);


        $query->select(join(",", $selects));

        return $query;
    }

    protected function allowedFilters($filters)
    {
        $filters = is_array($filters) ? $filters : func_get_args();

        $this->allowedFilters = collect($filters)->map(function ($filter) {
            if (is_array($filter) && array_key_exists('default', $filter)) {
                $default = $filter['default'];
            } else {
                $default = $filter;
            }

            return [
                'default' => $default,
                'rules'   => optional($filter)['rules'],
            ];
        });

        $this->ensureAllFiltersExist();
        $this->validateFilters();
        $this->addFiltersToQuery();

        return $this;
    }

    protected function ensureAllFiltersExist()
    {
        $filterNames = $this->request->filters()->keys();

        $allowedFilterNames = $this->allowedFilters->keys();

        $diff = $filterNames->diff($allowedFilterNames);

        if ($diff->count()) {
            throw InvalidFilterQuery::filtersNotAllowed($diff, $allowedFilterNames);
        }
    }

    protected function validateFilters()
    {
        $filters = $this->allowedFilters->filter(fn ($filter) => (bool) Arr::get($filter, 'rules'));

        $rules = $filters->map(fn ($filter) => $filter['rules'])->all();
        $values = $filters
            ->map(fn ($filter, $key) => $this->request->filters()->get($key, $filter['default']))
            ->all();

        validator($values, $rules)->validate();
    }

    protected function addFiltersToQuery()
    {
        $this->allowedFilters
            ->filter(fn ($filter, $key) => $this->isFilterRequested($key, $filter))
            ->each(function ($filter, $key) {
                $value = $this->request->filters()->get($key, $filter['default']);
                $this->setFilter($key, $value);
            });
    }

    protected function isFilterRequested($allowedFilter, $filter): bool
    {
        return $this->request->filters()->has($allowedFilter) || $filter['default'] !== null;
    }

    protected function setFilter($filter, $value)
    {
        $this->setFilters[$filter] = $value;
    }

    protected function defaultSorts($sorts)
    {
        if ($this->request->sorts()->isNotEmpty()) {
            // We've got requested sorts. No need to parse defaults.

            return $this;
        }

        $sorts = is_array($sorts) ? $sorts : func_get_args();

        collect($sorts)->each(function ($sort) {
            $this->setSort($sort);
        });

        return $this;
    }

    protected function setSort($sort)
    {
        $descending = $sort[0] === '-';

        $this->setSorts->add([
            'column'    => ltrim($sort, '-'),
            'direction' => $descending ? 'DESC' : 'ASC',
        ]);
    }

    protected function allowedSorts($sorts)
    {
        if ($this->request->sorts()->isEmpty()) {
            return $this;
        }

        $sorts = is_array($sorts) ? $sorts : func_get_args();

        $this->allowedSorts = collect($sorts);

        $this->ensureAllSortsExist();

        $this->addRequestedSortsToQuery();

        return $this;
    }

    protected function ensureAllSortsExist()
    {
        $requestedSortNames = $this->request->sorts()->map(function (string $sort) {
            return ltrim($sort, '-');
        });

        $allowedSortNames = $this->allowedSorts;

        $unknownSorts = $requestedSortNames->diff($allowedSortNames);

        if ($unknownSorts->isNotEmpty()) {
            throw InvalidSortQuery::sortsNotAllowed($unknownSorts, $allowedSortNames);
        }
    }

    protected function addRequestedSortsToQuery()
    {
        $this->request->sorts()->each(function (string $sort) {
            $this->setSort($sort);
        });
    }

    public function allowedAppends($appends): self
    {
        $appends = is_array($appends) ? $appends : func_get_args();

        $this->allowedAppends = collect($appends);

        $this->ensureAllAppendsExist();

        return $this;
    }

    protected function addAppendsToResults(Collection $results)
    {
        return $results->each(function (Model $result) {
            return $result->append($this->request->appends()->toArray());
        });
    }

    protected function ensureAllAppendsExist()
    {
        $appends = $this->request->appends();

        $diff = $appends->diff($this->allowedAppends);

        if ($diff->count()) {
            throw InvalidAppendQuery::appendsNotAllowed($diff, $this->allowedAppends);
        }
    }
}
