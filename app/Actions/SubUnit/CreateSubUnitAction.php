<?php

namespace App\Actions\SubUnit;

use App\Data\SubUnit\SubUnitData;
use App\Models\SubUnit;

class CreateSubUnitAction
{
    public function execute(SubUnitData $subUnitData): SubUnit
    {
        return SubUnit::create([
            'short_id'    => $subUnitData->shortId,
            'name'        => $subUnitData->name,
            'information' => $subUnitData->information,
        ]);
    }
}
