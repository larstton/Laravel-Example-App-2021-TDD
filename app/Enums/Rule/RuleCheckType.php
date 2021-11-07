<?php

namespace App\Enums\Rule;

use BenSampo\Enum\Enum;

/**
 * @method static static ServiceCheck()
 * @method static static WebCheck()
 * @method static static CustomCheck()
 * @method static static Cagent()
 * @method static static SnmpCheck()
 */
final class RuleCheckType extends Enum
{
    const ServiceCheck = 'serviceCheck';
    const WebCheck = 'webCheck';
    const CustomCheck = 'customCheck';
    const Cagent = 'cagent';
    const SnmpCheck = 'snmpCheck';

    public static function combineableCheckTypes()
    {
        return [
            [
                self::WebCheck(),
                self::ServiceCheck(),
            ],
            // Add more if needed, validation rule will handle it.
        ];
    }
}
