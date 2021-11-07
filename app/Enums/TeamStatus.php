<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Invited()
 * @method static static Joined()
 * @method static static Deleted()
 */
final class TeamStatus extends Enum
{
    const Invited = 'INVITED';
    const Joined = 'JOINED';
    const Deleted = 'DELETED';
}
