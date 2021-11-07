<?php

namespace Database\Factories;

use App\Data\Team\UpdateTeamData;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class UpdateTeamDataFactory
{
    use WithFaker;

    public static function make(array $params = []): UpdateTeamData
    {
        $faker = (new self)->makeFaker();

        return tap(new UpdateTeamData($data = array_merge([
            'name'                      => $faker->name,
            'timezone'                  => $faker->timezone,
            'defaultFrontman'           => null,
            'dateFormat'                => 'L.',
            'hasGrantedAccessToSupport' => false,
        ], $params)))->setHasData([
            'name'                      =>  Arr::has($data, 'name'),
            'timezone'                  =>  Arr::has($data, 'timezone'),
            'defaultFrontman'           =>  Arr::has($data, 'defaultFrontman'),
            'dateFormat'                =>  Arr::has($data, 'dateFormat'),
            'hasGrantedAccessToSupport' =>  Arr::has($data, 'hasGrantedAccessToSupport'),
        ]);
    }
}
