<?php

namespace App\Support\Influx\Facades;

use App\Support\Influx\InfluxRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin InfluxRepository
 */
class Influx extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'influx';
    }
}
