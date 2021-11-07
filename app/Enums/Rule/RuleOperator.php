<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static LessThan()
 * @method static static GreaterThan()
 * @method static static NotEqualTo()
 * @method static static EqualTo()
 * @method static static Empty()
 * @method static static NotEmpty()
 */
final class RuleOperator extends Enum
{
    const LessThan = '<';
    const GreaterThan = '>';
    const NotEqualTo = '<>';
    const EqualTo = '=';
    const Empty = 'empty';
    const NotEmpty = 'notEmpty';
}
