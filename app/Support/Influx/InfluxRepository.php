<?php

namespace App\Support\Influx;

use App\Models\Host;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InfluxDB\Client as InfluxClient;
use InfluxDB\Database as InfluxDB;
use InfluxDB\Query\Builder;


class InfluxRepository
{
    private InfluxClient $client;
    private InfluxDB $database;
    private InfluxKeyService $keyService;
    private array $databases;
    private string $defaultDatabase;

    public function __construct(
        InfluxClient $client,
        InfluxKeyService $keyService,
        array $databases,
        string $defaultDatabase
    ) {
        $this->client = $client;
        $this->keyService = $keyService;
        $this->databases = $databases;
        $this->defaultDatabase = $defaultDatabase;
    }

    public function getDefaultDatabase()
    {
        return $this->defaultDatabase;
    }

    public function getDatabases()
    {
        return $this->databases;
    }

    public function fetchByQueryBuilder(InfluxQueryBuilder $query): InfluxGroupedByHostCollection
    {
        $groupedQueries = $query->build();

        return $groupedQueries->map(function (InfluxGroupedByMetricCollection $queries) {
            if ($queries->count() == 1) {
                return InfluxGroupedByMetricCollection::make(
                    $queries->map(fn (Builder $query) => InfluxResultCollection::make(
                        $query->getResultSet()->getPoints()
                    ))->all()
                );
            }

            if ($queries->isEmpty()) {
                return InfluxGroupedByMetricCollection::make();
            }

            $results = $queries->map(fn (Builder $query) => InfluxResultCollection::make(
                $query->getResultSet()->getPoints()
            ));

            return InfluxGroupedByMetricCollection::ungroupResults($results);

        });

    }

    public function getAllMonitoredMetricsForHost(Host $host): Collection
    {
        return Cache::remember("metricsKey:{$host->id}", now()->addMinutes(5),
            fn () => $this->keyService->getHostKeys($host)
        );
    }

    public function buildQueryOnDatabase($database): Builder
    {
        return $this->setDatabase($database)->query();
    }

    public function query(): Builder
    {
        return $this->database->getQueryBuilder();
    }

    public function setDatabase($database): self
    {
        $this->database = $this->client->selectDB($database);

        return $this;
    }

    public function dropMeasurement($measurement)
    {
        $this->database->query("DROP MEASUREMENT \"{$measurement}\"");
    }

    public function __call($method, $params)
    {
        return $this->client->$method($params);
    }
}
