<?php

namespace App\Actions\SnmpCheck;

use App\Data\SnmpCheck\CreateSnmpCheckData;
use App\Models\Host;
use App\Models\SnmpCheck;
use App\Models\User;

class CreateSnmpCheckAction
{
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function execute(User $user, Host $host, CreateSnmpCheckData $snmpCheckData): SnmpCheck
    {
        return $host->snmpChecks()->create([
            'user_id'        => $user->id,
            'preset'         => $snmpCheckData->preset,
            'active'         => $snmpCheckData->active,
            'check_interval' => $snmpCheckData->checkInterval,
        ]);
    }
}
