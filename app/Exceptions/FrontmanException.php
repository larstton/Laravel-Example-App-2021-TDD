<?php

namespace App\Exceptions;

use App\Models\Team;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FrontmanException extends HttpException
{
    public static function frontmanInUse($message = 'This frontman is still in use. Detach all hosts first.')
    {
        return new self(403, $message);
    }

    public static function maximumFrontmenReached(Team $team)
    {
        return new self(403, "Maximum of allowed {$team->max_frontmen} frontmen reached.");
    }
}
