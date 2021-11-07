<?php

namespace App\Actions\StatusPage;

use App\Data\StatusPage\StatusPageData;
use App\Models\StatusPage;
use App\Models\User;

class CreateStatusPageAction
{
    public function execute(User $user, StatusPageData $statusPageData): StatusPage
    {
        return StatusPage::create([
            'title'   => $statusPageData->title,
            'team_id' => $user->team_id,
            'meta'    => $statusPageData->meta,
            'token'   => StatusPage::makeUniqueToken(6, 9),
        ]);
    }
}
