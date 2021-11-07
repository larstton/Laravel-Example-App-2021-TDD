<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Deactivated()
 * @method static static Active()
 */
final class HostActiveState extends Enum
{
    const Deactivated = 0;
    const Active = 1;
}
