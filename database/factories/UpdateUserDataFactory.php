<?php

namespace Database\Factories;

use App\Data\User\UpdateUserData;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class UpdateUserDataFactory
{
    use WithFaker;

    public static function make(array $params = []): UpdateUserData
    {
        $faker = (new self)->makeFaker();

        return tap(new UpdateUserData($data = array_merge([
            'nickname' => $faker->name,
            'name'     => $faker->name,
            'lang'     => 'en',
        ], $params)))->setHasData([
            'nickname' => Arr::has($data, 'nickname'),
            'name'     => Arr::has($data, 'name'),
            'lang'     => Arr::has($data, 'lang'),
        ]);
    }
}
