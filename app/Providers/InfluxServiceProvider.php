<?php

namespace App\Providers;

use App\Support\Influx\InfluxKeyService;
use App\Support\Influx\InfluxQueryBuilder;
use App\Support\Influx\InfluxQueryBuilderRequest;
use App\Support\Influx\InfluxRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use InfluxDB\Client as InfluxClient;

class InfluxServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(InfluxRepository::class, function ($app) {
            $config = config('influx.repository');
            $client = new InfluxClient(
                $config['host'],
                $config['port'],
                $config['username'],
                $config['password'],
            );

            return new InfluxRepository(
                $client,
                new InfluxKeyService,
                $config['databases'],
                $config['default_db']
            );
        });

        $this->app->bind(InfluxQueryBuilderRequest::class, function ($app) {
            return InfluxQueryBuilderRequest::fromRequest($app['request']);
        });

        $this->app->bind(InfluxQueryBuilder::class, function () {
            return new InfluxQueryBuilder(
                resolve(InfluxQueryBuilderRequest::class)
            );
        });

        $this->app->alias(InfluxRepository::class, 'influx');
    }

    public function boot()
    {
        //
    }

    public function provides()
    {
        return [
            'influx',
            InfluxRepository::class,
            InfluxQueryBuilderRequest::class,
            InfluxQueryBuilder::class,
        ];
    }
}
