<?php

namespace App\Actions\SubUnit;

use App\Data\SubUnit\SubUnitData;
use App\Models\SubUnit;

class UpdateSubUnitAction
{
    public function execute(SubUnit $subUnit, SubUnitData $subUnitData): SubUnit
    {
        $subUnit->update([
            'short_id'    => $subUnitData->shortId,
            'name'        => $subUnitData->name,
            'information' => $subUnitData->information,
        ]);

        return $subUnit;
    }
}
