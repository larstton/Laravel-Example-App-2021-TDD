<?php

namespace Database\Factories;

use App\Data\WebCheck\WebCheckData;
use Illuminate\Foundation\Testing\WithFaker;

class WebCheckDataFactory
{
    use WithFaker;

    public static function make(array $params = []): WebCheckData
    {
        $faker = (new self)->makeFaker();

        return new WebCheckData(array_merge([
            'path'                    => 'path',
            'protocol'                => 'https',
            'port'                    => 80,
            'method'                  => 'GET',
            'expectedPattern'         => null,
            'expectedPatternPresence' => 'present',
            'expectedHttpStatus'      => 200,
            'ignoreSSLErrors'         => false,
            'timeOut'                 => 5.0,
            'dontFollowRedirects'     => false,
            'searchHtmlSource'        => false,
            'checkInterval'           => 60,
            'active'                  => true,
            'preflight'               => false,
            'postData'                => null,
            'headers'                 => null,
            'headersMD5'              => null,
        ], $params));
    }
}
