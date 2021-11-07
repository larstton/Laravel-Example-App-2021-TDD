<?php

namespace App\Models;

use App\Casts\CheckLastSuccessStatusCast;
use App\Events\CustomCheck\CustomCheckCreated;
use App\Events\CustomCheck\CustomCheckDeleted;
use App\Events\CustomCheck\CustomCheckUpdated;
use App\Models\Concerns\HasUniqueToken;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @mixin IdeHelperCustomCheck
 */
class CustomCheck extends BaseModel
{
    use HasUniqueToken;

    protected $dispatchesEvents = [
        'created' => CustomCheckCreated::class,
        'updated' => CustomCheckUpdated::class,
        'deleted' => CustomCheckDeleted::class,
    ];

    protected $casts = [
        'expected_update_interval' => 'int',
        'last_checked_at'          => 'datetime',
        'last_success'             => CheckLastSuccessStatusCast::class,
    ];

    protected $appends = ['last_success'];

    /**
     * @return BelongsTo|Host
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * Will get the Team which owns the custom-check using the immediate
     * relationship of the host.
     *
     * CustomCheck [belongsTo] Host [belongsTo] Team
     *
     * @return HasOneThrough|Team
     */
    public function teamOwner()
    {
        return $this->hasOneThrough(
            Team::class,
            Host::class,
            'id', // Foreign key on hosts table...
            'id', // Foreign key on teams table...
            'host_id', // Local key on webchecks table...
            'team_id' // Local key on hosts table...
        );
    }
}
