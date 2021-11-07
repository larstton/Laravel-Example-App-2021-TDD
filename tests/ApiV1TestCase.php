<?php

namespace Tests;

use Tests\Concerns\AttachApiBearerToken;

abstract class ApiV1TestCase extends TestCase
{
    use AttachApiBearerToken;

    public $seed = true;

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if ($this->requestNeedsToken()) {
            $server = $this->attachToken($server);
        }

        return parent::call($method, env('API_URL').'/v1'.$uri, $parameters, $cookies, $files, $server, $content);
    }
}
