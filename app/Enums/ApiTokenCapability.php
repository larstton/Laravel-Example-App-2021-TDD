<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static RO()
 * @method static static RW()
 */
final class ApiTokenCapability extends Enum
{
    const RO = 'ro';
    const RW = 'rw';
}
