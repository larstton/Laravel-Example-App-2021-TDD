<?php

namespace App\Support\Influx;

use Illuminate\Support\Str;

class InfluxChartDataTransformer
{
    protected array $output;

    public function transform(InfluxResultCollection $results): array
    {
        $this->output = [];

        $needSubtract = request('subtract-from', false);

        $results->each(function ($value) use ($needSubtract) {
            collect($value)->keys()
                ->filter(fn ($point) => $this->isChartableData($value, $point))
                ->each(function ($point, $series) use ($value, $needSubtract) {
                    if (! isset($this->output[$series]['label'])) {
                        // Label may contain legend like (6.0 hour average).
                        // Extract it from label so it does not affect removing duplicates below.
                        $average = "";
                        if (preg_match('/( \(.*average\))/si', $point, $regs)) {
                            $average = $regs[1];
                        }

                        // Due to how influx generates column names eg. (ignoring AS and adding
                        // metric second time), there can be repeated strings in column name.
                        // So lets remove the repeated parts.
                        $label = str($point)
                            ->replace($average, "")
                            ->replace('.', '_')
                            ->explode("_")
                            ->filter()
                            ->reverse()
                            ->unique()
                            ->reverse()
                            ->join("_");

                        // Temperature graphs will not work if label does not end
                        // with " (critical threshold|temperature) temp".
                        $label = str($label)
                            ->replace(['temp_', '_', 'mean'], ['', ' ', ''])
                            ->trim();

                        // If label ends with "  temp" - this is temperature graph and we should not
                        // append legend, because temp graphs rely on sensor names returned by
                        // `/latest-data` endpoint, and that obviously don't have that legend
                        // part in the name.
                        if (! Str::is("* temp", $label)) {
                            // If string contained average - append it back to string.
                            $label = $label->append($average);
                        }

                        $this->output[$series]['label'] = (string) $label;
                        $this->output[$series]['average'] = $average;
                    }

                    if (! isset($this->output[$series]['type'])) {
                        $this->output[$series]['type'] = 'line';
                    }
                    $maxPointName = (string) Str::of($point)
                        ->replace('mean_', '__agg[max]__')
                        ->trim();

                    if (isset($value[$maxPointName])) {
                        if (! isset($this->output[$series]['max'])) {
                            $this->output[$series]['max'] = $value[$maxPointName];
                        }

                        $this->output[$series]['max'] = max($this->output[$series]['max'], $value[$maxPointName]);
                    }

                    $minPointName = (string) Str::of($point)
                        ->replace('mean_', '__agg[min]__')
                        ->trim();
                    if (isset($value[$minPointName])) {
                        if (! isset($this->output[$series]['min'])) {
                            $this->output[$series]['min'] = $value[$minPointName];
                        }

                        $this->output[$series]['min'] = min($this->output[$series]['min'], $value[$minPointName]);
                    }

                    $this->output[$series]['data'][] = [
                        'x' => strtotime($value['time']),
                        'y' => $needSubtract
                            ? $this->getYDataSubtracter($point) - $value[$point]
                            : $value[$point],
                    ];
                });
        });

        return array_values($this->output);
    }

    private function isChartableData($value, $point): bool
    {
        if ($point === 'time') {
            return false;
        }
        if (is_null($value[$point])) {
            return false;
        }
        if ($value[$point] < 0) {
            return false;
        }

        if (str($point)->startsWith('__agg[')) {
            return false;
        }

        return true;
    }

    private function getYDataSubtracter($point)
    {
        return request('subtract-from', function () use ($point) {
            if (Str::contains($point, ['mem.available_percent'])) {
                return 100;
            }

            return 0;
        });
    }
}
