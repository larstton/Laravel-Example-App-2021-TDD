<?php

namespace App\Models\Concerns;

use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait OwnedByTeam
{
    protected static $blockIfNoTeamSet = [
        'creating',
        'updating',
        'deleting',
        'restoring',
    ];

    public static function bootOwnedByTeam(): void
    {
        if (TenantManager::isEnabled()) {
            static::addGlobalScope('team', function (Builder $query) {
                TenantManager::guard();
                $query->whereIn((new self())->getTable().'.team_id', [
                    Team::currentTenant()->id,
                    '00000000-0000-0000-0000-000000000000',
                ]);
            });
        }

        collect(static::$blockIfNoTeamSet)->each(function ($event) {
            static::registerModelEvent($event, function (Model $model) {
                TenantManager::guard();
            });
        });

        static::saving(function (Model $model) {
            if (! isset($model->team_id)) {
                TenantManager::guard();
                $model->team_id = Team::currentTenant()->id;
            }
        });
    }

    /**
     * @return BelongsTo|Team
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeAllTeams(Builder $query)
    {
        return $query->withoutGlobalScope('team');
    }

    public function scopeWithoutTeamScope(Builder $query)
    {
        return $query->withoutGlobalScope('team');
    }
}
