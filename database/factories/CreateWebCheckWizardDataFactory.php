<?php

namespace Database\Factories;

use App\Data\Wizard\CreateWebCheckWizardData;
use Illuminate\Foundation\Testing\WithFaker;

class CreateWebCheckWizardDataFactory
{
    use WithFaker;

    public static function make(array $params = []): CreateWebCheckWizardData
    {
        $faker = (new self)->makeFaker();

        return new CreateWebCheckWizardData(array_merge([
            'url'       => $faker->url,
            'preflight' => false,
        ], $params));
    }
}
