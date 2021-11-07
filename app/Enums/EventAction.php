<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Alert()
 * @method static static Warning()
 * @method static static Snooze()
 * @method static static Ignore()
 */
final class EventAction extends Enum
{
    const Alert = 'alert';
    const Warning = 'warn';
    const Snooze = 'snooze';
    const Ignore = 'ignore';
}
