<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Agent()
 * @method static static WebCheck()
 * @method static static ServiceCheck()
 * @method static static SnmpCheck()
 * @method static static CustomCheck()
 */
final class CheckType extends Enum
{
    const Agent = 'cagent';
    const WebCheck = 'webCheck';
    const ServiceCheck = 'serviceCheck';
    const SnmpCheck = 'snmpCheck';
    const CustomCheck = 'customCheck';
}
