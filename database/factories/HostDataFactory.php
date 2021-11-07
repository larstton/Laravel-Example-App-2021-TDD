<?php

namespace Database\Factories;

use App\Data\Host\HostData;
use Illuminate\Foundation\Testing\WithFaker;

class HostDataFactory
{
    use WithFaker;

    public static function make(array $params = []): HostData
    {
        $faker = (new self)->makeFaker();

        return new HostData(array_merge([
            'name'        => $faker->name,
            'description' => $faker->sentence,
            'connect'     => '8.8.8.'.$faker->numberBetween(1, 255),
            'cagent'      => false,
            'dashboard'   => true,
            'muted'       => false,
            'active'      => true,
            'frontman'    => null,
            'subUnit'     => null,
            'tags'        => null,
            'snmpData'    => [],
        ], $params));
    }
}
