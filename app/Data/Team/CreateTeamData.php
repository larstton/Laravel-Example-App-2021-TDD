<?php

namespace App\Data\Team;

use App\Data\Auth\UserRegisterData;
use App\Data\BaseData;
use Carbon\Carbon;

class CreateTeamData extends BaseData
{
    public ?Carbon $trialEnd;
    public ?string $partner;
    public $partnerExtraData;
    public ?array $registrationTrack;

    public static function fromUserRegisterData(UserRegisterData $registerData): self
    {
        return new self([
            'trialEnd'          => $registerData->trialEnd,
            'partner'           => $registerData->partner,
            'partnerExtraData'  => $registerData->partnerExtraData,
            'registrationTrack' => $registerData->registrationTrack,
        ]);
    }
}
