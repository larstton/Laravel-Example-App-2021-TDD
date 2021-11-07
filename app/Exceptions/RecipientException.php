<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RecipientException extends HttpException
{
    public static function changingEmailIsForbidden($message = null)
    {
        $message ??= 'Editing e-mail address is forbidden';

        return new self(422, $message);
    }
}
