<?php

namespace App\Support\Validation;

class FQDN
{
    public static function isValidPrivate($input): bool
    {
        if (! self::isValid($input)) {
            return false;
        }

        return ! self::isValidPublicFQDN($input);
    }

    public static function isValid($input): bool
    {
        if (! strstr($input, '.')) {
            return false;
        }

        if (is_numeric(str_replace('.', '', $input))) {
            return false;
        }

        if (
            preg_match("/^([a-z\d]([-_]*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $input)
            && preg_match('/^.{1,253}$/', $input)
            && preg_match("/^[^.]{1,63}(\.[^.]{1,63})*$/", $input)) {
            return true;
        }

        return false;
    }

    public static function isValidPublicFQDN($input): bool
    {
        if (! self::isValid($input)) {
            return false;
        }

        if (preg_match("/\.local$/i", $input)) {
            return false;
        }

        return self::isResolvable($input);
    }

    /**
     * Check if a hostname is resolvable through the public DNS servers.
     * Relying on just one method is not enough.
     * For example dns_get_record() doesnt get any records for mail.owee.de while gethostbyname() does.
     *
     * @param $connect
     * @return bool
     */
    private static function isResolvable($connect): bool
    {
        if (count(dns_get_record($connect)) > 0) {
            return true;
        }
        if (gethostbyname($connect) != $connect) {
            return true;
        }

        return false;
    }

    public static function isInvalid($input): bool
    {
        return ! self::isValid($input);
    }
}
