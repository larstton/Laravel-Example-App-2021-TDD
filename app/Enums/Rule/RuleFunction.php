<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static Last()
 * @method static static Sum()
 * @method static static Average()
 * @method static static Minimum()
 * @method static static Maximum()
 */
final class RuleFunction extends Enum
{
    const Last = 'last';
    const Sum = 'sum';
    const Average = 'avg';
    const Minimum = 'min';
    const Maximum = 'max';
}
