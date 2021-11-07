<?php

namespace Database\Factories;

use App\Data\Host\HostData;
use App\Data\Team\TeamManagementUpdateData;
use App\Enums\TeamPlan;
use Illuminate\Foundation\Testing\WithFaker;

class TeamManagementUpdateDataFactory
{
    use WithFaker;

    public static function make(array $params = []): TeamManagementUpdateData
    {
        $faker = (new self)->makeFaker();

        return new TeamManagementUpdateData(array_merge([
            'plan'             => TeamPlan::Trial(),
            'maxHosts'         => 99,
            'maxRecipients'    => 99,
            'dataRetention'    => 30,
            'maxMembers'       => 99,
            'maxFrontmen'      => 99,
            'minCheckInterval' => 60,
            'currency'         => 'EUR',
        ], $params));
    }
}
