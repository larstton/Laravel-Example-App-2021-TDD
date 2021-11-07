<?php

namespace App\Actions\Team;

use App\Data\Team\CreateTeamData;
use App\Enums\TeamPlan;
use App\Models\Frontman;
use App\Models\Team;
use App\Support\Tracking\RegistrationTrackService;
use Carbon\Carbon;

class CreateTeamAction
{
    public function execute(CreateTeamData $createTeamData): Team
    {
        return Team::create([
            'timezone'            => 'NOTSET',
            'default_frontman_id' => Frontman::DEFAULT_FRONTMAN_UUID,
            'data_retention'      => 30,
            'max_hosts'           => 999,
            'max_frontmen'        => 99,
            'max_members'         => 99,
            'plan'                => TeamPlan::Trial(),
            'min_check_interval'  => 60,
            'trial_ends_at'       => $this->setTrialEndDate($createTeamData),
            'partner'             => $createTeamData->partner,
            'partner_extra_data'  => $createTeamData->partnerExtraData,
            'registration_track'  => RegistrationTrackService::parseTrackingData(
                $createTeamData->registrationTrack
            ),
        ]);
    }

    private function setTrialEndDate(CreateTeamData $createTeamData): Carbon
    {
        $trialEndDate = $createTeamData->trialEnd ?? now()->addDays(15);

        if ($createTeamData->partner === 'plesk') {
            $trialEndDate = now()->addDays(30);
        }

        return $trialEndDate;
    }
}
