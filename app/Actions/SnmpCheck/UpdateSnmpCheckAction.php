<?php

namespace App\Actions\SnmpCheck;

use App\Data\SnmpCheck\UpdateSnmpCheckData;
use App\Enums\CheckLastSuccess;
use App\Models\SnmpCheck;

class UpdateSnmpCheckAction
{
    public function execute(SnmpCheck $snmpCheck, UpdateSnmpCheckData $updateSnmpCheckData): SnmpCheck
    {
        if (! is_null($updateSnmpCheckData->active)) {
            $snmpCheck->active = $updateSnmpCheckData->active;
        }

        if (! is_null($updateSnmpCheckData->checkInterval)) {
            $snmpCheck->check_interval = $updateSnmpCheckData->checkInterval;
        }

        if ($snmpCheck->isDirty()) {
            $snmpCheck->last_success = CheckLastSuccess::Pending();
        }

        $snmpCheck->save();

        return $snmpCheck;
    }
}
