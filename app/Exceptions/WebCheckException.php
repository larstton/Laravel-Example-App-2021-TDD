<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class WebCheckException extends HttpException
{
    public static function checkExistsForTeam($message = null)
    {
        $message ??= "There is already an identical check for this host.";

        return new self(422, $message);
    }
}
