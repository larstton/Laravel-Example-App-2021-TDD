<?php

namespace Database\Factories;

use App\Data\SnmpCheck\CreateSnmpCheckData;
use App\Data\SnmpCheck\UpdateSnmpCheckData;
use Illuminate\Foundation\Testing\WithFaker;

class UpdateSnmpCheckDataFactory
{
    use WithFaker;

    public static function make(array $params = []): UpdateSnmpCheckData
    {
        $faker = (new self)->makeFaker();

        return new UpdateSnmpCheckData(array_merge([
            'preset'        => 'basedata',
            'checkInterval' => 60,
            'active'        => true,
        ], $params));
    }
}
