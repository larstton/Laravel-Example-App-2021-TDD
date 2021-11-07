<?php

namespace App\Actions\ApiToken;

use App\Data\ApiToken\ApiTokenData;
use App\Models\ApiToken;

class CreateApiTokenAction
{
    public function execute(ApiTokenData $apiTokenData): ApiToken
    {
        return ApiToken::create([
            'name'       => $apiTokenData->name,
            'capability' => $apiTokenData->capability,
            'token'      => ApiToken::makeUniqueToken(30, 48),
        ]);
    }
}
