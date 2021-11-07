<?php

namespace App\Support\Influx;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InfluxMetric
{
    public static function isTextMetric($metric): bool
    {
        $array = ['text', 'message', 'error', 'warning', 'log', 'alert'];

        return (bool) Arr::first($array, fn ($ending) => preg_match('/^.*'.$ending.'$/U', $metric));
    }

    public static function normaliseMetric(string $metric, Collection $allMetrics, string $database): ?string
    {
        if (Str::contains($metric, 'keys/')) {
            $metric = str_replace('keys/', '', $metric);
        }

        // Aliases metrics 'net.total_out_B_per_s','net.total_in_B_per_s' used to show network card speed
        // as old cagents do not have them.
        if (in_array($metric, ['net.total_out_B_per_s', 'net.total_in_B_per_s'])
            && empty($allMetrics[$database][$metric])
        ) {
            foreach ($allMetrics[$database] as $metricName => $measurement) {
                if ($metric === 'net.total_out_B_per_s' && Str::contains($metricName, 'net.out_B_per_s')) {
                    $metric = $metricName;
                    break;
                }
                if ($metric === 'net.total_in_B_per_s' && Str::contains($metricName, 'net.in_B_per_s')) {
                    $metric = $metricName;
                    break;
                }
            }
        }

        return $metric;
    }
}
