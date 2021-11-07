<?php

namespace App\Models;

use App\Enums\ApiTokenCapability;
use App\Models\Concerns\AuthedEntity;
use App\Models\Concerns\HasUniqueToken;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Spatie\Activitylog\Traits\CausesActivity;

/**
 * @mixin IdeHelperApiToken
 */
class ApiToken extends BaseModel implements AuthedEntity, AuthenticatableContract
{
    use OwnedByTeam, CastsEnums, LogsActivity, HasUniqueToken, Authenticatable, CausesActivity;

    protected $dates = [
        'last_used_at',
    ];

    protected $enumCasts = [
        'capability' => ApiTokenCapability::class,
    ];

    protected function setActivityLogAction(string $eventName): string
    {
        return "API token '{$this->name}' [{$this->capability->value}] {$eventName}";
    }
}
