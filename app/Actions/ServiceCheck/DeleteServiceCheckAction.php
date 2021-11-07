<?php

namespace App\Actions\ServiceCheck;

use App\Jobs\ServiceCheck\DeleteServiceCheck;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\User;

class DeleteServiceCheckAction
{
    public function execute(User $user, ServiceCheck $serviceCheck, Host $host): void
    {
        DeleteServiceCheck::dispatchIf($serviceCheck->delete(), $user, $serviceCheck, $host);
    }
}
