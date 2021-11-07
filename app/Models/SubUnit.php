<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSubUnit
 */
class SubUnit extends BaseModel
{
    use OwnedByTeam, LogsActivity;

    /**
     * @return HasMany|Host
     */
    public function hosts()
    {
        return $this->hasMany(Host::class)
            ->whereScopedByUserHostTag(current_user())
            ->whereScopedByUserSubUnit(current_user());
    }

    protected function setActivityLogAction(string $eventName): string
    {
        $name = $this->name ?? '';

        return "Sub unit '{$name}' {$eventName}.";
    }
}
