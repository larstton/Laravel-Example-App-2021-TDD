<?php

namespace App\Models;

use App\Events\JobmonResult\JobmonResultCreated;
use App\Events\JobmonResult\JobmonResultDeleted;
use App\Events\JobmonResult\JobmonResultUpdated;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @mixin IdeHelperJobmonResult
 */
class JobmonResult extends Model
{
    use LogsActivity, HasFactory;

    const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
    ];

    protected $dispatchesEvents = [
        'created' => JobmonResultCreated::class,
        'updated' => JobmonResultUpdated::class,
        'deleted' => JobmonResultDeleted::class,
    ];

    /**
     * @return BelongsTo|Host
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    public function scopeWhereHostIdAndGroupedByJobIdWithCount(Builder $query, Host $host)
    {
        return $query
            ->select(['data', 'host_id', 'jobmon_results.id', 'job_id', 'result_count', 'created_at'])
            ->joinSub(function (QueryBuilder $query) use ($host) {
                $query->selectRaw('max(id) AS id, count(*) as result_count')
                    ->from('jobmon_results')
                    ->where('host_id', $host->id)
                    ->whereNotNull('host_id')
                    ->groupBy('job_id');
            }, 'sub', 'jobmon_results.id', '=', 'sub.id', 'right');
    }

    public function scopeWhereBetweenDates(Builder $query, string $from, string $to)
    {
        $from = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

        return $query->whereBetween('created_at', [$from, $to]);
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return "Job '{$this->job_id}' data {$eventName}.";
    }
}
