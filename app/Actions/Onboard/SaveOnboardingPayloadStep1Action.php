<?php

namespace App\Actions\Onboard;

use App\Data\Onboard\OnboardStep1Data;
use App\Models\Team;

class SaveOnboardingPayloadStep1Action
{
    public function execute(OnboardStep1Data $data, Team $team)
    {
        $team->update([
            'timezone'            => $data->timezone,
            'default_frontman_id' => $data->defaultFrontman->id,
            'date_format'         => $data->dateFormat,
        ]);
    }
}
