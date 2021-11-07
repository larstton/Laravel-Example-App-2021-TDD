<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static Warn()
 * @method static static Alert()
 * @method static static Snooze()
 * @method static static Ignore()
 */
final class RuleAction extends Enum
{
    const Warn = 'warn';
    const Alert = 'alert';
    const Snooze = 'snooze';
    const Ignore = 'ignore';
}
