<?php

namespace App\Models;

use App\Enums\CheckType;
use App\Enums\EventAction;
use App\Enums\EventState;
use App\Enums\HostActiveState;
use App\Events\Host\HostCreated;
use App\Events\Host\HostDeleted;
use App\Events\Host\HostUpdated;
use App\Models\Concerns\HasTags;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use App\Models\Concerns\PurgesCache;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperHost
 */
class Host extends BaseModel
{
    use OwnedByTeam, LogsActivity, SoftDeletes, CastsEnums, HasTags, PurgesCache;

    public $appends = [];

    protected $dispatchesEvents = [
        'created' => HostCreated::class,
        'updated' => HostUpdated::class,
        'deleted' => HostDeleted::class,
    ];

    protected $casts = [
        'inventory'    => 'array',
        'hw_inventory' => 'array',
        'cagent'       => 'bool',
        'dashboard'    => 'bool',
        'muted'        => 'bool',
        'active'       => 'bool',
        'snmp_timeout' => 'int',
        'snmp_port'    => 'int',
    ];

    protected $dates = [
        'cagent_last_updated_at',
        'snmp_check_last_updated_at',
        'web_check_last_updated_at',
        'service_check_last_updated_at',
        'custom_check_last_updated_at',
    ];

    protected $enumCasts = [
        'active' => HostActiveState::class,
    ];

    public static function getHashOfAllTeamsHosts(): string
    {
        $hash = self::selectRaw(
                "GROUP_CONCAT(`id`, `dashboard` ORDER BY `created_at` SEPARATOR '') as hash"
            )->get('hash')[0]->hash ?? '';

        return md5(Str::of($hash)->trim()->lower());
    }

    public static function availableServiceChecks(): array
    {
        return config('service-checks');
    }

    public static function getTagListForActiveHosts(): Collection
    {
        return Tag::withType('host')
            ->withCount('hosts')
            ->whereHas('hosts', fn ($query) => $query
                ->active()
                ->whereScopedByUserHostTag(current_user())
                ->whereScopedByUserSubUnit(current_user())
            )
            ->get();
    }

    public static function getGroupListForActiveHosts(): Collection
    {
        return Tag::withType('host')
            ->containing(':')
            ->whereHas('hosts', fn ($query) => $query
                ->active()
                ->whereScopedByUserHostTag(current_user())
                ->whereScopedByUserSubUnit(current_user())
            )
            ->get();
    }

    public function getExtendedTags(): array
    {
        return $this->tags->map(fn (Tag $tag) => [
            'name'                       => $tag->name,
            'usedForEventRouting'        => collect(
                $tag->meta->get('recipient_filtering', [])
            )->isNotEmpty(),
            'usedForGuestAccessLimiting' => collect(
                $tag->meta->get('team_member_filtering', [])
            )->isNotEmpty(),
        ])->toArray();
    }

    public function scopeActive(Builder $query)
    {
        $query->where('active', 1);
    }

    public function scopeInactive(Builder $query)
    {
        $query->where('active', 0);
    }

    public function scopeWithAgent(Builder $query)
    {
        $query->where('cagent', 1);
    }

    public function scopeOnlyDashboardVisible(Builder $query, $value = true)
    {
        if ($value) {
            $query->where('dashboard', true);
        }
    }

    public function scopeWhereScopedByUserHostTag(Builder $query, ?User $user = null)
    {
        $query->when(filled(optional($user)->host_tag), function ($query) use ($user) {
            $query->withAnyTags([$user->host_tag], Host::getTagType());
        });
    }

    public function scopeWhereScopedByUserSubUnit(Builder $query, ?User $user = null)
    {
        $query->when(filled(optional($user)->sub_unit_id), function ($query) use ($user) {
            $query->where('hosts.sub_unit_id', $user->sub_unit_id);
        });
    }

    public function scopeWhereHasActiveEvents(Builder $query, $value = true)
    {
        if ($value) {
            $query->whereHas('events', function (Builder $query) {
                $query->whereColumn('hosts.id', '=', 'events.affected_host_id')
                    ->where('events.state', EventState::Active());
            });
        }
    }

    public function scopeWhereHasAllTags(Builder $query, ...$tags)
    {
        $query->withAllTags($tags, self::getTagType());
    }

    public function scopeWhereHasAnyTags(Builder $query, ...$tags)
    {
        $query->withAnyTags($tags, self::getTagType());
    }

    public function scopeWhereHasAllGroupTags(Builder $query, ...$groups)
    {
        $query->whereAllTagsBeginWithString(Arr::wrap($groups), self::getTagType());
    }

    public function scopeWhereHasAnyGroupTags(Builder $query, ...$groups)
    {
        $query->whereAnyTagBeginsWithString(Arr::wrap($groups), self::getTagType());
    }

    /**
     * @return BelongsTo|Frontman
     */
    public function frontman()
    {
        return $this->belongsTo(Frontman::class);
    }

    /**
     * @return HasMany|HostHistory
     */
    public function histories()
    {
        return $this->hasMany(HostHistory::class);
    }

    /**
     * @return HasMany|CheckResult
     */
    public function agentCheckResults()
    {
        return $this->checkResults()->where('check_type', CheckType::Agent());
    }

    /**
     * @return HasMany|CheckResult
     */
    public function checkResults()
    {
        return $this->hasMany(CheckResult::class);
    }

    /**
     * @return HasMany|CpuUtilisationSnapshot
     */
    public function cpuUtilisationSnapshots()
    {
        return $this->hasMany(CpuUtilisationSnapshot::class);
    }

    /**
     * @return HasMany|JobmonResult
     */
    public function jobmonResultsGroupedByJobId(): HasMany
    {
        return $this->jobmonResults()->whereHostIdAndGroupedByJobIdWithCount($this);
    }

    /**
     * @return HasMany|JobmonResult
     */
    public function jobmonResults()
    {
        return $this->hasMany(JobmonResult::class);
    }

    /**
     * @return HasMany|Event
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return BelongsTo|SubUnit
     */
    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function getSummaryAttribute(): HostSummary
    {
        return new HostSummary($this);
    }

    public function getEventSummaryAttribute(): HostEventSummary
    {
        return new HostEventSummary($this);
    }

    /**
     * Gets the very latest check date from all the check results linked to active checks for host.
     * To use this you should apply the following scope to your query for best performance:
     * - withCheckCount().
     *
     * @return null|Carbon
     */
    public function getLastCheckTime(): ?Carbon
    {
        // If team only has custom checks then just return now.
        if (isset($this->check_count_total)) {
            $totalCheckCount = $this->check_count_total;
            $customCheckCount = $this->custom_checks_count;
        } else {
            $totalCheckCount = $this->total_checks_count;
            $customCheckCount = $this->customChecks()->count();
        }

        if (($totalCheckCount - $customCheckCount) === 0 && $customCheckCount > 0) {
            return now();
        }

        $max = collect([
            $this->web_check_last_updated_at,
            $this->service_check_last_updated_at,
            $this->snmp_check_last_updated_at,
            $this->custom_check_last_updated_at,
            $this->cagent_last_updated_at,
        ])->max(fn ($v) => strtotime($v));

        return $max ? Carbon::createFromTimestamp($max) : null;
    }

    /**
     * @return HasMany|CustomCheck
     */
    public function customChecks()
    {
        return $this->hasMany(CustomCheck::class);
    }

    public function resolveRouteBinding($value, $field = null): self
    {
        return Host::resolveFullyLoaded(new Host([
            'id' => $value,
        ]));
    }

    public static function resolveFullyLoaded(Host $host): self
    {
        return Host::query()
            ->where('hosts.id', $host->id)
            ->withCheckCount()
            ->withSnmpLastUpdatedAt()
            ->with('frontman', 'subUnit')
            ->firstOrFail();
    }

    public function getTotalChecksCountAttribute(): int
    {
        $count = 0;
        if ($this->relationLoaded('webChecks')) {
            $count += $this->webChecks->count();
        } else {
            $count += $this->webChecks()->count();
        }
        if ($this->relationLoaded('snmpChecks')) {
            $count += $this->snmpChecks->count();
        } else {
            $count += $this->snmpChecks()->count();
        }
        if ($this->relationLoaded('customChecks')) {
            $count += $this->customChecks->count();
        } else {
            $count += $this->customChecks()->count();
        }
        if ($this->relationLoaded('serviceChecks')) {
            $count += $this->serviceChecks->count();
        } else {
            $count += $this->serviceChecks()->count();
        }

        if ($this->usesMonitoringAgent()) {
            $count += 1;
        }

        return $count;
    }

    /**
     * @return HasMany|WebCheck
     */
    public function webChecks()
    {
        return $this->hasMany(WebCheck::class);
    }

    /**
     * @return HasMany|SnmpCheck
     */
    public function snmpChecks()
    {
        return $this->hasMany(SnmpCheck::class);
    }

    /**
     * @return HasMany|ServiceCheck
     */
    public function serviceChecks()
    {
        return $this->hasMany(ServiceCheck::class);
    }

    public function usesMonitoringAgent(): bool
    {
        return $this->cagent;
    }

    public function getLatestCheckDateAttribute(): Carbon
    {
        return self::withoutGlobalScopes()
                ->selectRaw('GREATEST(max_1, max_2, max_3, max_4, max_5) as `latest_check_at`')
                ->fromRaw(value(function () {
                    $sqlWeb = CheckResult::query()
                        ->selectRaw('COALESCE(max(data_updated_at),0) as max_1')
                        ->whereIn('check_id', function (QueryBuilder $query) {
                            $query->select('id')->from('web_checks')->where('host_id', $this->id);
                        })->take(1)->toFullSql();

                    $sqlService = CheckResult::query()
                        ->selectRaw('COALESCE(max(data_updated_at),0) as max_2')
                        ->whereIn('check_id', function (QueryBuilder $query) {
                            $query->select('id')->from('service_checks')->where('host_id', $this->id);
                        })->take(1)->toFullSql();

                    $sqlSnmp = CheckResult::query()
                        ->selectRaw('COALESCE(max(data_updated_at),0) as max_3')
                        ->whereIn('check_id', function (QueryBuilder $query) {
                            $query->select('id')->from('snmp_checks')->where('host_id', $this->id);
                        })->take(1)->toFullSql();

                    $sqlCustom = CheckResult::query()
                        ->selectRaw('COALESCE(max(data_updated_at),0) as max_4')
                        ->whereIn('check_id', function (QueryBuilder $query) {
                            $query->select('id')->from('custom_checks')->where('host_id', $this->id);
                        })->take(1)->toFullSql();

                    $sqlAgent = self::query()
                        ->selectRaw('COALESCE(max(cagent_last_updated_at),0) as max_5')
                        ->where('id', $this->id)
                        ->take(1)->toFullSql();

                    return "({$sqlWeb}) as `web`, ({$sqlService}) as `service`, ({$sqlSnmp}) as `snmp`, ({$sqlCustom}) as `custom`, ({$sqlAgent}) as `agent`";
                }))->withCasts([
                    'latest_check_at' => 'datetime',
                ])->first('latest_check_at')->latest_check_at ?? now();
    }

    public function scopeWithSnmpLastUpdatedAt(Builder $query)
    {
        $query
            ->addSelect([
                'snmp_last_checked_at' => SnmpCheck::select('snmp_checks.last_checked_at')
                    ->whereColumn('snmp_checks.host_id', 'hosts.id')
                    ->orderBy('snmp_checks.last_checked_at', 'desc')
                    ->limit(1),
            ])->withCasts([
                'snmp_last_checked_at' => 'datetime',
            ]);
    }

    /**
     * Adds on the following fields to each retrieved model:
     * - has_icmp_check (1 or 0)
     * - service_checks_count
     * - web_checks_count
     * - custom_checks_count
     * - snmp_checks_count
     * - agent_check_count ( 1 or 0 - based on cagent field )
     * - check_count_total (sum of above).
     *
     * @param  Builder  $query
     */
    public function scopeWithCheckCount(Builder $query)
    {
        $icmpSubQuery = ServiceCheck::selectRaw('1')
            ->whereColumn('service_checks.host_id', 'hosts.id')
            ->where('protocol', 'icmp')
            ->limit(1)
            ->toFullSql();

        $query
            ->leftJoin('service_checks', 'service_checks.host_id', '=', 'hosts.id')
            ->leftJoin('web_checks', 'web_checks.host_id', '=', 'hosts.id')
            ->leftJoin('custom_checks', 'custom_checks.host_id', '=', 'hosts.id')
            ->leftJoin('snmp_checks', 'snmp_checks.host_id', '=', 'hosts.id')
            ->select([
                'hosts.*',
                DB::raw("IFNULL(({$icmpSubQuery}), 0) as `has_icmp_check`"),
                DB::raw('COUNT(DISTINCT (service_checks.`id`)) AS service_checks_count'),
                DB::raw('COUNT(DISTINCT (web_checks.`id`)) AS web_checks_count'),
                DB::raw('COUNT(DISTINCT (custom_checks.`id`)) AS custom_checks_count'),
                DB::raw('COUNT(DISTINCT (snmp_checks.`id`)) AS snmp_checks_count'),
                DB::raw('`hosts`.`cagent` AS agent_check_count'),
                DB::raw('COUNT(DISTINCT (service_checks.`id`)) + COUNT(DISTINCT (web_checks.`id`)) + COUNT(DISTINCT (custom_checks.`id`)) + COUNT(DISTINCT (snmp_checks.`id`)) + `hosts`.`cagent` AS check_count_total'),
            ])
            ->groupBy('hosts.id');
    }

    // /**
    //  * Appends the following properties to each retrieved model instance:.
    //  *
    //  * - web_last_check_at: latest check date of host's web checks, or 0
    //  * - service_last_check_at: latest check date of host's service checks, or 0
    //  * - snmp_last_check_at: latest check date of host's snmp checks, or 0
    //  * - custom_last_check_at: latest check date of host's custom checks, or 0
    //  *
    //  * @param  Builder  $query
    //  */
    // public function scopeWithLatestCheckAtDate(Builder $query)
    // {
    //     $query->addSelect([
    //         'web_last_check_at'     => CheckResult::query()
    //             ->selectRaw('COALESCE(max(`data_updated_at`), 0)')
    //             ->whereColumn('host_id', 'hosts.id')
    //             ->whereIn('check_id', function ($query) {
    //                 $query->select('id')->from('web_checks')->whereColumn('host_id', 'hosts.id');
    //             })
    //             ->take(1),
    //         'service_last_check_at' => CheckResult::query()
    //             ->selectRaw('COALESCE(max(`data_updated_at`), 0)')
    //             ->whereColumn('host_id', 'hosts.id')
    //             ->whereIn('check_id', function ($query) {
    //                 $query->select('id')->from('service_checks')->whereColumn('host_id', 'hosts.id');
    //             })
    //             ->take(1),
    //         'snmp_last_check_at'    => CheckResult::query()
    //             ->selectRaw('COALESCE(max(`data_updated_at`), 0)')
    //             ->whereColumn('host_id', 'hosts.id')
    //             ->whereIn('check_id', function ($query) {
    //                 $query->select('id')->from('snmp_checks')->whereColumn('host_id', 'hosts.id');
    //             })
    //             ->take(1),
    //         'custom_last_check_at'  => CheckResult::query()
    //             ->selectRaw('COALESCE(max(`data_updated_at`), 0)')
    //             ->whereColumn('host_id', 'hosts.id')
    //             ->whereIn('check_id', function ($query) {
    //                 $query->select('id')->from('custom_checks')->whereColumn('host_id', 'hosts.id');
    //             })
    //             ->take(1),
    //     ]);
    // }

    public function scopeWithActiveEventsConstrained(
        Builder $query,
        $limitToActions = null,
        $limitCommentsToGuestVisible = false
    ) {
        $eventCommentSql = $limitCommentsToGuestVisible
            ? 'COALESCE(SUM(`event_comments`.`visible_to_guests`), 0)'
            : 'COUNT(`event_comments`.`id`)';

        $query->with([
            'events' => function ($query) use ($eventCommentSql, $limitToActions) {
                $query
                    ->leftJoin('event_comments', 'events.id', '=', 'event_comments.event_id')
                    ->where('events.state', EventState::Active())
                    ->where('events.action', '!=', EventAction::Ignore())
                    ->when(! is_null($limitToActions),
                        function ($query) use ($limitToActions) {
                            $query->whereIn('events.action', $limitToActions);
                        }
                    )
                    ->select([
                        'events.*',
                        DB::raw("{$eventCommentSql} as comment_count"),
                    ])
                    ->groupBy('events.id');
            },
            'events.rule',
        ]);
    }

    public function purgeableEvents(): array
    {
        return [
            'created' => 'host-'.$this->id,
            'updated' => 'host-'.$this->id,
            'deleted' => 'host-'.$this->id,
        ];
    }

    protected function shouldLogActivity(string $eventName): bool
    {
        if ($this->isForceDeleting()) {
            return false;
        }

        return true;
    }

    protected function setActivityLogAction(string $eventName): string
    {
        $changedAttributes = $this->getChanges();
        logger()->info('Host changes', $changedAttributes);


        if (Arr::exists($changedAttributes, 'dashboard')) {
            $appendedText[] = ($changedAttributes['dashboard'] ? 'Added to' : 'Removed from ').' Dashboard';
        }
        if (Arr::exists($changedAttributes, 'muted')) {
            $appendedText[] = 'Alerting turned '.($changedAttributes['muted'] ? 'off' : 'on');
        }


        if (! is_null($this->connect)) {
            $text = "Host {$this->name} ({$this->connect}) {$eventName}";
        } else {
            $text = "Host {$this->name} {$eventName}";
        }

        if (! empty($appendedText)) {
            $text .= ": ".join(", ", $appendedText);
        }

        return $text;
    }
}
