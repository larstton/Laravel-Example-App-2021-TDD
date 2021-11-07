<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Recovered()
 * @method static static Active()
 */
final class EventState extends Enum
{
    const Recovered = 0;
    const Active = 1;

    public static function getDescription($value): string
    {
        if ($value === self::Active) {
            return 'The event is active.';
        }

        if ($value === self::Recovered) {
            return 'The event has been recovered. The issue has been resolved.';
        }

        return parent::getDescription($value);
    }
}
