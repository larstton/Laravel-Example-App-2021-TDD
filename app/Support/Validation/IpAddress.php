<?php

namespace App\Support\Validation;

use Symfony\Component\HttpFoundation\IpUtils;

class IpAddress
{
    protected static $ipv4PrivateRanges = [
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
    ];

    protected static $ipv6PrivateRanges = [
        'fc00::/7',
        'fd00::/8',
    ];

    public static function isPrivateIpv4($input): bool
    {
        return self::isValidPrivate($input) && self::isV4($input);
    }

    /**
     * Will return true if a valid IP and private.
     *
     * @param $input
     * @return bool
     */
    public static function isValidPrivate($input): bool
    {
        $ips = self::isV4($input) ? self::$ipv4PrivateRanges : self::$ipv6PrivateRanges;

        return self::isValid($input) && IpUtils::checkIp($input, $ips);
    }

    public static function isV4($input): bool
    {
        return ! self::isV6($input);
    }

    public static function isV6($input): bool
    {
        return substr_count($input, ':') > 1;
    }

    public static function isValid(string $input): bool
    {
        return self::isValidIpv4($input) || self::isValidIpv6($input);
    }

    public static function isValidIpv4(string $input): bool
    {
        return (bool) filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    public static function isValidIpv6(string $input): bool
    {
        return (bool) filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public static function isInvalid(string $input): bool
    {
        return ! self::isValid($input);
    }

    public static function isPrivateIpv6(string $input): bool
    {
        return self::isValidPrivate($input) && self::isV6($input);
    }

    public static function isPublicIpv4($input): bool
    {
        return self::isValidPublicIP($input) && self::isV4($input);
    }

    /**
     * Will return true if a valid IP and public.
     *
     * @param $input
     * @return bool
     */
    public static function isValidPublicIP($input): bool
    {
        return self::isValid($input) && ! self::isValidPrivate($input);
    }

    public static function isPublicIpv6(string $input): bool
    {
        return self::isValidPublicIP($input) && self::isV6($input);
    }
}
