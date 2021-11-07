<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CheckPreflightException extends Exception
{
    private $console;

    public function __construct($message = '', $console = [], Throwable $previous = null)
    {
        parent::__construct($message, 200, $previous);
        $this->console = $console;
    }

    final public function getConsole()
    {
        return $this->console;
    }
}
