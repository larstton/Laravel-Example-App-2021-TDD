<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static Byte()
 * @method static static KiloByte()
 * @method static static MegaByte()
 * @method static static GigaByte()
 * @method static static TeraByte()
 * @method static static Second()
 * @method static static Minute()
 * @method static static Hour()
 * @method static static Day()
 */
final class RuleThresholdUnit extends Enum
{
    const Byte = 'B';
    const KiloByte = 'KB';
    const MegaByte = 'MB';
    const GigaByte = 'GB';
    const TeraByte = 'TB';
    const Second = 's';
    const Minute = 'm';
    const Hour = 'h';
    const Day = 'd';
}
