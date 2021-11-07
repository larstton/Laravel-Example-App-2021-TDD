<?php

namespace App\Http\Controllers\Graph;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Support\Influx\InfluxRepository;
use Illuminate\Support\Str;

class ListMonitoredMetricsForHostController extends Controller
{
    public function __invoke(Host $host, InfluxRepository $influx)
    {
        $data = $influx->getAllMonitoredMetricsForHost($host)
            ->mapWithKeys(function ($keys, $db) {
                return [Str::replaceFirst('Results', '', $db) => array_keys($keys)];
            })
            ->toArray();

        return $this->json(compact('data'));
    }
}
