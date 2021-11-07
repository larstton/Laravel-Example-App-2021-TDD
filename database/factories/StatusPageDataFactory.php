<?php

namespace Database\Factories;

use App\Data\StatusPage\StatusPageData;
use Illuminate\Foundation\Testing\WithFaker;

class StatusPageDataFactory
{
    use WithFaker;

    public static function make(array $params = []): StatusPageData
    {
        $faker = (new self)->makeFaker();

        return new StatusPageData(array_merge([
            'title' => $faker->name,
            'meta'  => [],
        ], $params));
    }
}
