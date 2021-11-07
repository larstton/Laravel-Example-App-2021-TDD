<?php

/** @noinspection PhpUnused */

namespace Tests\Concerns;

use Illuminate\Support\Facades\Artisan;

trait MultiDatabaseMigrations
{
    public function setUpTraits()
    {
        $this->swapTestingDatabaseConfiguration();
        Artisan::call('db:create');
        parent::setUpTraits();
    }

    protected function swapTestingDatabaseConfiguration()
    {
        $driver = config('database.default');
        $database = config("database.connections.{$driver}.database");

        config([
            "database.connections.{$driver}.database" => sprintf('%s%s', $database, env('TEST_TOKEN', '')),
        ]);
    }
}
