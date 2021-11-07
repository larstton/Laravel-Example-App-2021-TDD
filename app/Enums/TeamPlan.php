<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Free()
 * @method static static Trial()
 * @method static static Payg()
 * @method static static Frozen()
 * @method static static Pro()
 */
final class TeamPlan extends Enum
{
    const Free = 'free'; // deprecated but still in DB
    const Trial = 'trial';
    const Payg = 'payg';
    const Frozen = 'frozen';
    const Pro = 'pro';
}
