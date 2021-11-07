<?php

namespace Database\Factories;

use App\Data\Team\UpdateTeamMemberData;
use App\Enums\TeamMemberRole;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class UpdateTeamMemberDataFactory
{
    use WithFaker;

    public static function make(array $params = []): UpdateTeamMemberData
    {
        $faker = (new self)->makeFaker();

        return tap(new UpdateTeamMemberData($data = array_merge([
            'role'    => TeamMemberRole::Admin(),
            'hostTag' => null,
            'subUnit' => null,
        ], $params)))->setHasData([
            'role'    => Arr::has($data, 'role'),
            'hostTag' => Arr::has($data, 'hostTag'),
            'subUnit' => Arr::has($data, 'subUnit'),
        ]);
    }
}
