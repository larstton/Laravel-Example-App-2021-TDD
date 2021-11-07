<?php

namespace Database\Factories;

use App\Data\Team\CreateTeamData;
use Illuminate\Foundation\Testing\WithFaker;

class CreateTeamDataFactory
{
    use WithFaker;

    public static function make(array $params = []): CreateTeamData
    {
        $faker = (new self)->makeFaker();

        return new CreateTeamData(array_merge([
            'trialEnd'          => now()->addDays(7),
            'partner'           => null,
            'partnerExtraData'  => null,
            'registrationTrack' => [],
        ], $params));
    }
}
