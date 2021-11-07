<?php

namespace App\Actions\SnmpCheck;

use App\Jobs\SnmpCheck\DeleteSnmpCheck;
use App\Models\Host;
use App\Models\SnmpCheck;
use App\Models\User;

class DeleteSnmpCheckAction
{
    public function execute(User $user, SnmpCheck $snmpCheck, Host $host): void
    {
        DeleteSnmpCheck::dispatchIf($snmpCheck->delete(), $user, $snmpCheck, $host);
    }
}
