<?php

namespace App\Actions\ServiceCheck;

use App\Enums\CheckLastSuccess;
use App\Models\ServiceCheck;

class UpdateServiceCheckAction
{
    public function execute(ServiceCheck $serviceCheck, $active, $checkInterval): ServiceCheck
    {
        $serviceCheck->update([
            'active'         => $active,
            'check_interval' => $checkInterval,
            'last_success'   => CheckLastSuccess::Pending(),
        ]);

        return $serviceCheck;
    }
}
