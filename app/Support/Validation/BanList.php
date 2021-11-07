<?php

namespace App\Support\Validation;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BanList
{
    public static function isConnectBanned($connect): bool
    {
        return self::isInBanList($connect, collect(config('banned.domains')));
    }

    private static function isInBanList($value, Collection $banned): bool
    {
        if ($banned->isEmpty()) {
            return false;
        }

        return $banned
            ->filter(fn ($domain) => Str::is($domain, $value))
            ->isNotEmpty();
    }

    public static function isEmailBanned($email): bool
    {
        return self::isInBanList($email, collect(config('banned.emails')));
    }
}
