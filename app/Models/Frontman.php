<?php

namespace App\Models;

use App\Events\Frontman\FrontmanCreated;
use App\Events\Frontman\FrontmanDeleted;
use App\Events\Frontman\FrontmanUpdated;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use App\Models\Concerns\PurgesCache;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

/**
 * @mixin IdeHelperFrontman
 */
class Frontman extends BaseModel
{
    use OwnedByTeam, PurgesCache, LogsActivity;

    const DEFAULT_FRONTMAN_UUID = '00000000-0000-0000-0000-000000000000';

    protected $dispatchesEvents = [
        'created' => FrontmanCreated::class,
        'updated' => FrontmanUpdated::class,
        'deleted' => FrontmanDeleted::class,
    ];

    protected $dates = [
        'last_heartbeat_at',
    ];

    public function scopePublic(Builder $query)
    {
        $query->where('frontmen.team_id', self::DEFAULT_FRONTMAN_UUID);
    }

    public function scopePrivate(Builder $query)
    {
        $query->where('frontmen.team_id', '!=', self::DEFAULT_FRONTMAN_UUID);
    }

    public function validForTeam(Team $team): bool
    {
        return in_array($this->team_id, [$team->id, self::DEFAULT_FRONTMAN_UUID]);
    }

    /**
     * @return HasMany|Host
     */
    public function hosts()
    {
        return $this->hasMany(Host::class);
    }

    /**
     * @param  User|null  $user
     * @return HasMany|Host
     * @throws Throwable
     */
    public function filteredHosts(?User $user = null)
    {
        throw_if(is_null($user) && is_null($user = current_user()), Exception::class);

        return $this->hasMany(Host::class)
            ->whereScopedByUserHostTag($user)
            ->whereScopedByUserSubUnit($user);
    }

    public function getTypeAttribute(): string
    {
        return $this->isPublic() ? 'public' : 'private';
    }

    public function isPublic(): bool
    {
        return $this->team_id === self::DEFAULT_FRONTMAN_UUID;
    }

    public function isPrivate()
    {
        return ! $this->isPublic();
    }

    public function purgeableEvents(): array
    {
        return [
            'created' => 'frontman-'.$this->id,
            'updated' => 'frontman-'.$this->id,
            'deleted' => 'frontman-'.$this->id,
        ];
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return "Frontman {$this->location} {$eventName}";
    }
}
