<?php

namespace Database\Factories;

use App\Data\SubUnit\SubUnitData;
use Illuminate\Foundation\Testing\WithFaker;

class SubUnitDataFactory
{
    use WithFaker;

    public static function make(array $params = []): SubUnitData
    {
        $faker = (new self)->makeFaker();

        return new SubUnitData(array_merge([
            'shortId'    => 'shortId',
            'name'        => $faker->name,
            'information' => $faker->sentence,
        ], $params));
    }
}
