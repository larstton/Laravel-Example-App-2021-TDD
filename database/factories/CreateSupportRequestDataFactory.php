<?php

namespace Database\Factories;

use App\Data\Support\CreateSupportRequestData;
use Illuminate\Foundation\Testing\WithFaker;

class CreateSupportRequestDataFactory
{
    use WithFaker;

    public static function make(array $params = []): CreateSupportRequestData
    {
        $faker = (new self)->makeFaker();

        return new CreateSupportRequestData(array_merge([
            'subject'    => $faker->name,
            'body'       => $faker->paragraph,
            'attachment' => [],
        ], $params));
    }
}
