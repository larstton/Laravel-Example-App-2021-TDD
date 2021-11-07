<?php

namespace App\Actions\StatusPage;

use App\Data\StatusPage\StatusPageData;
use App\Models\StatusPage;

class UpdateStatusPageAction
{
    public function execute(StatusPage $statusPage, StatusPageData $statusPageData)
    {
        $statusPage->update([
            'title' => $statusPageData->title,
            'meta'  => $statusPageData->meta,
        ]);

        return $statusPage;
    }
}
