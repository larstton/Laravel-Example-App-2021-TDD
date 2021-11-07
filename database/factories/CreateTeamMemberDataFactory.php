<?php

namespace Database\Factories;

use App\Data\Team\CreateTeamMemberData;
use App\Enums\TeamMemberRole;
use Illuminate\Foundation\Testing\WithFaker;

class CreateTeamMemberDataFactory
{
    use WithFaker;

    public static function make(array $params = []): CreateTeamMemberData
    {
        $faker = (new self)->makeFaker();

        return new CreateTeamMemberData(array_merge([
            'email'           => $faker->email,
            'role'            => TeamMemberRole::Admin(),
            'createRecipient' => false,
        ], $params));
    }
}
