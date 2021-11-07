<?php

namespace Tests\Concerns;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait AttachJwtToken
{
    protected ?User $loginUser = null;

    /**
     * @param  User  $user
     * @return $this
     */
    public function loginAs(User $user)
    {
        $this->loginUser = $user;

        return $this;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if ($this->requestNeedsToken()) {
            $server = $this->attachToken($server);
        }

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * @return bool
     */
    protected function requestNeedsToken()
    {
        return ! is_null($this->loginUser);
    }

    /**
     * @param  array  $server
     * @return array
     */
    protected function attachToken(array $server)
    {
        return array_merge($server, $this->transformHeadersToServerVars([
            'Authorization' => 'Bearer '.$this->getJwtToken(),
        ]));
    }

    /**
     * @return string
     */
    protected function getJwtToken()
    {
        return JWTAuth::fromUser($this->loginUser);
    }
}
