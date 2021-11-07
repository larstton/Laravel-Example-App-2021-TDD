<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static Name()
 * @method static static UUID()
 * @method static static Connect()
 * @method static static Tag()
 * @method static static None()
 */
final class RuleHostMatchPart extends Enum
{
    const Name = 'name';
    const UUID = 'uuid';
    const Connect = 'connect';
    const Tag = 'tag';
    const None = 'none';
}
