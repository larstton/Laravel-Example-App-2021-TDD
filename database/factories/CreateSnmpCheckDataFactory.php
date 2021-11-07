<?php

namespace Database\Factories;

use App\Data\SnmpCheck\CreateSnmpCheckData;
use Illuminate\Foundation\Testing\WithFaker;

class CreateSnmpCheckDataFactory
{
    use WithFaker;

    public static function make(array $params = []): CreateSnmpCheckData
    {
        $faker = (new self)->makeFaker();

        return new CreateSnmpCheckData(array_merge([
            'preset'        => 'basedata',
            'checkInterval' => 60,
            'active'        => true,
        ], $params));
    }
}
