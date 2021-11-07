<?php

namespace App\Models;

use App\Enums\CheckType;
use App\Models\Concerns\HasAssociatedChecks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperCheckResult
 */
class CheckResult extends Model
{
    use HasAssociatedChecks, HasFactory;

    const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = [
        'data_updated_at',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    public function scopeHostChecksByType(Builder $query, Host $host, CheckType $type)
    {
        return $query->joinSub(function ($query) use ($type, $host) {
            $query->selectRaw('max(id) AS id')
                ->from('check_results')
                ->where('host_id', $host->id)
                ->whereNotNull('host_id')
                ->where('check_type', $type)
                ->groupBy('check_id');
        }, 'sub', 'check_results.id', '=', 'sub.id');
    }

    /**
     * @return MorphTo|WebCheck|ServiceCheck|SnmpCheck|CustomCheck
     */
    public function check()
    {
        return $this->morphTo();
    }
}
