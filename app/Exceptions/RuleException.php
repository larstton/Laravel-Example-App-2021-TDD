<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RuleException extends HttpException
{
    public static function duplicateRuleNotAllowed($message = 'A team cannot have the same rule registered twice.')
    {
        return new self(403, $message);
    }
}
