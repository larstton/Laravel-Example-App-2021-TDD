<?php

namespace App\Actions\CustomCheck;

use App\Data\CustomCheck\CustomCheckData;
use App\Models\CustomCheck;

class UpdateCustomCheckAction
{
    public function execute(CustomCheck $customCheck, CustomCheckData $customCheckData): CustomCheck
    {
        $customCheck->update([
            'name'                     => $customCheckData->name,
            'expected_update_interval' => $customCheckData->expectedUpdateInterval,
        ]);

        return $customCheck;
    }
}
