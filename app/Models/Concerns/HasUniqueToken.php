<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUniqueToken
{
    public static function makeUniqueToken($min = 9, $max = 12, $column = 'token')
    {
        $token = null;
        while (true) {
            $token = Str::random(rand($min, $max));
            if (self::where($column, $token)->count() === 0) {
                break;
            }
        }

        return $token;
    }
}
