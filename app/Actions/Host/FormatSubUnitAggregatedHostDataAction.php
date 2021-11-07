<?php

namespace App\Actions\Host;

use App\Data\Host\AggregatedHostDataData;
use App\Models\SubUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FormatSubUnitAggregatedHostDataAction extends FormatAggregatedHostDataAction
{
    public function execute(LengthAwarePaginator $aggregatedData): LengthAwarePaginator
    {
        $lengthAwarePaginator = $aggregatedData;

        return $lengthAwarePaginator->setCollection($aggregatedData->map(function (SubUnit $subUnit) {
            return $this->collateAndTransform($subUnit, [
                'id'      => $subUnit->id,
                'shortId' => $subUnit->short_id,
            ], 'sub-unit');
        })->filter(fn (AggregatedHostDataData $data) => $data->hostsCount > 0));
    }
}
