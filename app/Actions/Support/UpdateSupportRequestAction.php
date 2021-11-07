<?php

namespace App\Actions\Support;

use App\Enums\SupportRequestState;
use App\Models\SupportRequest;

class UpdateSupportRequestAction
{
    public function execute(SupportRequest $supportRequest, SupportRequestState $state)
    {
        $supportRequest->update([
            'state' => $state,
        ]);

        return $supportRequest;
    }
}
