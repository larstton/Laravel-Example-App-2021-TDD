<?php

namespace App\Support\Tenancy\Exceptions;

use LogicException;

class MissingTenancy extends LogicException
{
    public static function make()
    {
        return new self('No tenancy has been set so this request was blocked. Fix this in the code.', 500);
    }
}
