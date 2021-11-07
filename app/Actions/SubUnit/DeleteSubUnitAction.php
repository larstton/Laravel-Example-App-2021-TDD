<?php

namespace App\Actions\SubUnit;

use App\Models\Host;
use App\Models\SubUnit;
use Illuminate\Support\Facades\DB;

class DeleteSubUnitAction
{
    public function execute(SubUnit $subUnit)
    {
        DB::transaction(function () use ($subUnit) {
            Host::where('sub_unit_id', $subUnit->id)->get()->each->update([
                'sub_unit_id' => null,
            ]);

            $subUnit->delete();
        });
    }
}
