<?php

namespace App\Http\Controllers\ApiToken;

use App\Actions\ApiToken\CreateApiTokenAction;
use App\Data\ApiToken\ApiTokenData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiToken\CreateApiTokenRequest;
use App\Http\Resources\ApiTokenResource;
use App\Models\ApiToken;

class ApiTokenController extends Controller
{
    public function index()
    {
        $this->authorize(ApiToken::class);

        return ApiTokenResource::collection(ApiToken::latest()->get());
    }

    public function store(CreateApiTokenRequest $request, CreateApiTokenAction $createApiTokenAction)
    {
        $this->authorize(ApiToken::class);

        $newApiToken = $createApiTokenAction->execute(ApiTokenData::fromRequest($request));

        return ApiTokenResource::make($newApiToken);
    }

    public function show(ApiToken $apiToken)
    {
        $this->authorize($apiToken);

        return ApiTokenResource::make($apiToken);
    }

    public function destroy(ApiToken $apiToken)
    {
        $this->authorize($apiToken);

        $apiToken->delete();

        return $this->noContent();
    }
}
