<?php

namespace App\Actions\Host;

use App\Data\Host\AggregatedHostDataData;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FormatGroupAggregatedHostDataAction extends FormatAggregatedHostDataAction
{
    public function execute(LengthAwarePaginator $aggregatedData): LengthAwarePaginator
    {
        $lengthAwarePaginator = $aggregatedData;

        return $lengthAwarePaginator->setCollection($aggregatedData->map(function (Tag $tag) {
            return $this->collateAndTransform($tag, $tag->name, 'group');
        })->filter(fn (AggregatedHostDataData $data) => $data->hostsCount > 0));
    }
}
