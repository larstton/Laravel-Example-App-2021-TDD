<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Open()
 * @method static static InProgress()
 * @method static static Closed()
 */
final class SupportRequestState extends Enum
{
    const Open = 'open';
    const InProgress = 'in_progress';
    const Closed = 'closed';
}
