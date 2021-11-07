<?php

namespace App\Actions\Auth;

use App\Actions\ApiToken\CreateApiTokenAction;
use App\Data\ApiToken\ApiTokenData;
use App\Enums\ApiTokenCapability;
use App\Models\ApiToken;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Support\Facades\DB;

class ConfigureNewPleskTeamPostVerificationAction
{
    private CreateApiTokenAction $createApiTokenAction;

    public function __construct(CreateApiTokenAction $createApiTokenAction)
    {
        $this->createApiTokenAction = $createApiTokenAction;
    }

    public function execute(Team $team): ApiToken
    {
        return DB::transaction(function () use ($team) {
            TenantManager::setCurrentTenant($team);

            // Set default frontman to EU-WEST for all plesk users.
            $team->update([
                'default_frontman_id' => config('cloudradar.frontman.base_frontmen.eu_west'),
            ]);

            $name = 'Plesk server';

            if (! is_null($token = ApiToken::whereName($name)->whereTeamId($team->id)->first())) {
                return $token;
            }

            return $this->createApiTokenAction->execute(new ApiTokenData([
                'name'       => $name,
                'capability' => ApiTokenCapability::RW(),
            ]));
        });
    }
}
