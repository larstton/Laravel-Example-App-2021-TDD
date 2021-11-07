<?php

namespace App\Actions\Frontman;

use App\Models\Frontman;

class UpdateFrontmanAction
{
    public function execute(Frontman $frontman, $location)
    {
        return tap($frontman)->update(compact('location'));
    }
}
