<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NotChecked()
 * @method static static Failed()
 * @method static static Success()
 * @method static static Pending()
 * @method static static NoData()
 */
final class CheckLastSuccess extends Enum
{
    const NotChecked = null;
    const Failed = 0;
    const Success = 1;
    const Pending = 3;
    const NoData = 4;
}
