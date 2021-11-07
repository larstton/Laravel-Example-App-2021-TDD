<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class HostException extends HttpException
{
    public static function hostWithNameExistsForTeam($name, $message = null)
    {
        $message ??= "A host with the name '{$name}' already exists.";

        return new self(422, $message);
    }

    public static function hostWithServiceOrWebChecksCannotHaveEmptyConnect($message = null)
    {
        $message ??= "You cannot remove the FQDN/IP from a host with service and/or web checks.";

        return new self(422, $message);
    }

    public static function hostWithConnectExistsForTeam($connect, $message = null)
    {
        $message ??= "A host with the connect (FQDN/IP) '{$connect}' already exists.";

        return new self(422, $message);
    }

    public static function invalidConnectProvided($connect, $message = null)
    {
        $message ??= "'{$connect}' is not a valid FQDN/IP.";

        return new self(422, $message);
    }

    public static function invalidPublicConnectProvided($connect, $message = null)
    {
        $message ??= "'{$connect}' is not a valid public FQDN/IP.";

        return new self(422, $message);
    }

    public static function bannedConnectProvided($connect, $message = null)
    {
        $message ??= "'{$connect}' domain is not allowed.";

        return new self(422, $message);
    }

    public static function invalidSnmpSettings($message)
    {
        return new self(422, $message);
    }
}
