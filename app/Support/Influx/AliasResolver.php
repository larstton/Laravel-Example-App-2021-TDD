<?php

namespace App\Support\Influx;

use Illuminate\Support\Collection;

class AliasResolver
{
    public static function resolveAliasToMetricGroup($alias): array
    {
        return self::getMetricAliasList()->get($alias, []);
    }

    public static function getMetricAliasList(): Collection
    {
        return collect([
            'quick-view-graph-data'         => [
                // metric => measurement
                'fs.total_read_B_per_s'   => 'fs.*',
                'fs.total_write_B_per_s'  => 'fs.*',
                'cpu.util.user.1.total'   => 'cpu.*',
                'cpu.util.system.1.total' => 'cpu.*',
                'mem.available_percent'   => 'mem.*',
                'net.total_in_B_per_s'    => 'net.*',
                'net.total_out_B_per_s'   => 'net.*',
            ],
            'mini-graph-data'               => [
                // metric => measurement
                'cpu.util.idle.1.total' => 'cpu.*',
                'mem.available_percent' => 'mem.*',
            ],
            'fs_percent'                    => [
                'free_percent' => 'fs.*',
            ],
            'fs_free_bytes'                 => [
                'free_B' => 'fs.*',
            ],
            'fs_total_bytes'                => [
                'total_B' => 'fs.*',
            ],
            'cpu_utilization_total_percent' => [
                '.total' => 'cpu.*',
            ],
            'memory_usage_percent'          => [
                '_percent' => 'mem.*',
            ],
            'memory_bytes'                  => [
                '_B' => 'mem.*',
            ],
            'temperature'                   => [
                '.temp' => 'temperatures.list',
            ],
            'net_per_s'                     => [
                '_B_per_s' => 'net.*',
            ],
        ]);
    }
}
