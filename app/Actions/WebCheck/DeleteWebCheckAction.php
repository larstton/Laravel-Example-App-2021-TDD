<?php

namespace App\Actions\WebCheck;

use App\Jobs\WebCheck\DeleteWebCheck;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;

class DeleteWebCheckAction
{
    public function execute(User $user, WebCheck $webCheck, Host $host): void
    {
        DeleteWebCheck::dispatchIf($webCheck->delete(), $user, $webCheck, $host);
    }
}
