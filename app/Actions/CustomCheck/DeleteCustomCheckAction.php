<?php

namespace App\Actions\CustomCheck;

use App\Jobs\CustomCheck\DeleteCustomCheck;
use App\Models\CustomCheck;
use App\Models\Host;
use App\Models\User;

class DeleteCustomCheckAction
{
    public function execute(User $user, CustomCheck $customCheck, Host $host): void
    {
        $isLastCheck = CustomCheck::whereHostId($host->id)->count() === 1;
        DeleteCustomCheck::dispatchIf(
            $customCheck->delete(),
            $user, $customCheck, $host, $isLastCheck
        );
    }
}
