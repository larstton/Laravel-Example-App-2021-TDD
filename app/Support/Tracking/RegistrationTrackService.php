<?php

namespace App\Support\Tracking;

class RegistrationTrackService
{
    /**
     * Read and deconstruct the registration track cookie
     * Example:
     * {
     * "fv_r": "https://www.google.com/",
     * "fv_t": 1592990482,
     * "fv_u": "https://try.cloudradar.io/?
     * utm_source=Google&utm_medium=cpc&
     * utm_campaign=MonitisWW&
     * utm_content=web performance monitoring&
     * gclid=CjwKCAjw88v3BRBFEiwApw"
     * }
     * @param $track
     * @return false|string|null
     */
    public static function parseTrackingData(?array $track)
    {
        if (is_null($track)) {
            return null;
        }

        if (array_key_exists('ctrack', $track) && ! is_null($track['ctrack'])) {
            $track += self::deconstructRegistrationTrackArray(
                json_decode($track['ctrack'], true)
            );
        }

        unset($track['ctrack']);

        if (array_key_exists('ga', $track) && is_null($track['ga'])) {
            unset($track['ga']);
        }

        if (blank($track)) {
            return null;
        }

        return json_encode($track);
    }

    public static function deconstructRegistrationTrackArray(?array $track): array
    {
        if (! array_key_exists('fv_u', $track)) {
            return $track;
        }
        if (! strpos($track['fv_u'], '/?')) {
            return $track;
        }

        [$track['reg_site'], $utm] = explode('?', $track['fv_u']);
        foreach (explode('&', $utm) as $utmItem) {
            [$utmKey, $utmValue] = explode('=', $utmItem);
            $track[$utmKey] = $utmValue;
        }

        return $track;
    }
}
