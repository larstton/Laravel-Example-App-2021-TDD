<?php

namespace App\Http\Transformers;

use Carbon\Carbon;

class DateTransformer
{
    public static function transform(?Carbon $date)
    {
        if (is_null($date)) {
            return null;
        }

        $team = current_team();
        $localDate = clone $date;
        $timezone = rescue(function () use ($team, $localDate) {
            $localDate->setTimezone($team->timezone);

            return $team->timezone;
        }, fn () => 'unknown (UTC fallback)', false);

        return [
            'ISO-8601'  => $date->toIso8601String(),
            'formatted' => format_carbon_to_team_format($team, $date),
            'readable'  => $date->format('D, M j Y, H:i T'),
            'timestamp' => $date->getTimestamp(),
            'local'     => [
                'ISO-8601'  => $localDate->toIso8601String(),
                'formatted' => format_carbon_to_team_format($team, $localDate),
                'readable'  => $localDate->format('D, M j Y, H:i T'),
                'timestamp' => $localDate->getTimestamp(),
                'timezone'  => $timezone,
            ],
        ];
    }
}
