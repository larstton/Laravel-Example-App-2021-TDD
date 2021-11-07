<?php

namespace App\Data\ApiToken;

use App\Data\BaseData;
use App\Enums\ApiTokenCapability;
use App\Http\Requests\ApiToken\CreateApiTokenRequest;

class ApiTokenData extends BaseData
{
    public string $name;
    public ApiTokenCapability $capability;

    public static function fromRequest(CreateApiTokenRequest $request): self
    {
        return new self([
            'name'       => $request->name,
            'capability' => ApiTokenCapability::coerce($request->capability),
        ]);
    }
}
