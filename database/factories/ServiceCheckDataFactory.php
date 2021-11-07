<?php

namespace Database\Factories;

use App\Data\ServiceCheck\ServiceCheckData;
use Illuminate\Foundation\Testing\WithFaker;

class ServiceCheckDataFactory
{
    use WithFaker;

    public static function make(array $params = []): ServiceCheckData
    {
        $faker = (new self)->makeFaker();

        return new ServiceCheckData(array_merge([
            'protocol'      => 'tcp',
            'checkInterval' => 60,
            'service'       => 'https',
            'port'          => 80,
            'active'        => true,
            'preflight'     => false,
        ], $params));
    }
}
