<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TeamException extends HttpException
{
    public static function invalidFrontman($frontmanUuid, $message = null)
    {
        $message ??= "The given frontman UUID '{$frontmanUuid}' does not exist or does not belong to your team.";

        return new self(403, $message);
    }

    public static function noDefaultFrontman($message = 'No default frontman set for the team.')
    {
        return new self(403, $message);
    }

    public static function requirementNotMet($message = null)
    {
        $message ??= 'The action cannot be performed because one or more requirements are not met.';

        return new self(422, $message);
    }

    public static function bannedEmailProvided($email, $message = null)
    {
        $message ??= "E-mail '{$email}' is not allowed.";

        return new self(422, $message);
    }

    public static function trialExpired($message = null)
    {
        $message ??= 'Trial expired';

        return new self(422, $message);
    }

    public static function maximumHostsReached($message = null)
    {
        $message ??= 'Maximum hosts reached';

        return new self(422, $message);
    }
}
