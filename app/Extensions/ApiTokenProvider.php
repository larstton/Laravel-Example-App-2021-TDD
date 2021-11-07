<?php

namespace App\Extensions;

use App\Models\ApiToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ApiTokenProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        abort(500);
    }

    public function retrieveByToken($identifier, $token)
    {
        abort(500);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        abort(500);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $bearer = $credentials['api_token'];

        return ApiToken::withoutTeamScope()->whereToken($bearer)->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        abort(500);
    }
}
