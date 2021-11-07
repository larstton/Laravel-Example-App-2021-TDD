<?php

namespace App\Models;

use App\Enums\CheckType;
use App\Enums\TeamMemberRole;
use App\Enums\TeamPlan;
use App\Models\Concerns\HasMeta;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\PurgesCache;
use App\Support\Tenancy\Concerns\Tenantable;
use App\Support\Tenancy\Contracts\IsTenant;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTeam
 */
class Team extends BaseModel implements IsTenant
{
    use CastsEnums, Tenantable, SoftDeletes, PurgesCache, HasMeta, LogsActivity;

    protected static $recordEvents = ['updated'];

    protected $enumCasts = [
        'plan'          => TeamPlan::class,
        'previous_plan' => TeamPlan::class,
    ];

    protected $casts = [
        'has_granted_access_to_support' => 'bool',
        'onboarded'                     => 'bool',
        'has_created_host'              => 'bool',
    ];

    protected $dates = [
        'plan_last_changed_at',
        'trial_ends_at',
        'upgraded_at',
    ];

    /**
     * @return HasMany|TeamMember
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'team_id');
    }

    /**
     * @return HasMany|Rule
     */
    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * @return HasMany|Event
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return HasMany|EventComment
     */
    public function eventComments()
    {
        return $this->hasMany(EventComment::class);
    }

    /**
     * @return HasMany|TeamSetting
     */
    public function teamSettings()
    {
        return $this->hasMany(TeamSetting::class);
    }

    /**
     * @return HasMany|ActivityLog
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * @return HasMany|SubUnit
     */
    public function subUnits()
    {
        return $this->hasMany(SubUnit::class);
    }

    /**
     * @return HasMany|UserAgentData
     */
    public function userAgentData()
    {
        return $this->hasMany(UserAgentData::class);
    }

    /**
     * @return HasManyThrough|UserSetting
     */
    public function userSettings()
    {
        return $this->hasManyThrough(
            UserSetting::class,
            User::class,
            'team_id',
            'user_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|JobmonResult
     */
    public function jobMonResults()
    {
        return $this->hasManyThrough(
            JobmonResult::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|WebCheck
     */
    public function webChecks()
    {
        return $this->hasManyThrough(
            WebCheck::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|WebCheck
     */
    public function serviceChecks()
    {
        return $this->hasManyThrough(
            ServiceCheck::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|CustomCheck
     */
    public function customChecks()
    {
        return $this->hasManyThrough(
            CustomCheck::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|SnmpCheck
     */
    public function snmpChecks()
    {
        return $this->hasManyThrough(
            SnmpCheck::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasMany|CheckResult
     */
    public function agentCheckResults()
    {
        return $this->checkResults()->where('check_type', CheckType::Agent());
    }

    /**
     * @return HasManyThrough|CheckResult
     */
    public function checkResults()
    {
        return $this->hasManyThrough(
            CheckResult::class,
            Host::class,
            'team_id',
            'host_id',
            'id',
            'id'
        );
    }

    /**
     * @return HasManyThrough|Reminder
     */
    public function reminders()
    {
        return $this->hasManyThrough(
            Reminder::class,
            Event::class,
            'team_id',
            'event_id',
            'id',
            'id'
        );
    }


    /**
     * @return HasMany|ApiToken
     */
    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * @return HasMany|PaidMessageLog
     */
    public function paidMessageLog()
    {
        return $this->hasMany(PaidMessageLog::class);
    }

    /**
     * @return HasMany|Recipient
     */
    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }

    /**
     * @return HasMany|Frontman
     */
    public function frontmen()
    {
        return $this->hasMany(Frontman::class);
    }

    /**
     * @return HasMany|HostHistory
     */
    public function hostHistories()
    {
        return $this->hasMany(HostHistory::class);
    }

    /**
     * @return HasMany|StatusPage
     */
    public function statusPages()
    {
        return $this->hasMany(StatusPage::class);
    }

    /**
     * @return HasOne|Frontman
     */
    public function defaultFrontman()
    {
        return $this->hasOne(Frontman::class, 'id', 'default_frontman_id')
            ->withDefault(
                new Frontman([
                    'id' => Frontman::DEFAULT_FRONTMAN_UUID,
                ])
            );
    }

    /**
     * @return BelongsTo|User
     */
    public function originalTeamMember()
    {
        // original_team_member_id
        return $this->belongsTo(User::class);
    }

    public function scopeWithOriginalTeamMember(Builder $query)
    {
        $query->addSelect([
            'original_team_member_id' => User::select('id')
                ->whereColumn('team_id', 'teams.id')
                ->oldest()
                ->take(1),
        ])->with('originalTeamMember');
    }

    /**
     * @return HasMany|User
     */
    public function admins()
    {
        return $this->hasMany(User::class)->whereRole(TeamMemberRole::Admin());
    }

    public function getTrialDaysRemainingAttribute()
    {
        if ($this->isOnTrial()) {
            return $this->trial_ends_at->endOfDay()->diffInDays(now()->endOfDay());
        }

        return 0;
    }

    public function isOnTrial(): bool
    {
        if (is_null($this->trial_ends_at)) {
            return false;
        }

        return $this->trial_ends_at->greaterThan(now());
    }

    public function getIsNewTeamAttribute(): bool
    {
        return ! $this->has_created_host;
    }

    public function isUpgradingToPaygPlan(TeamPlan $newPlan): bool
    {
        return $this->plan->isNot(TeamPlan::Payg()) && $newPlan->is(TeamPlan::Payg());
    }

    public function isDowngradingFromPaygPlan(TeamPlan $newPlan): bool
    {
        return $this->isOnPaygPlan() && $newPlan->isNot(TeamPlan::Payg());
    }

    public function isOnPaygPlan(): bool
    {
        return $this->plan->is(TeamPlan::Payg());
    }

    public function isOnPaidPlan(): bool
    {
        return $this->isOnPaygPlan() || $this->plan->is(TeamPlan::Pro());
    }

    public function isUnfreezingPlan(TeamPlan $newPlan): bool
    {
        return $this->plan->is(TeamPlan::Frozen()) && $newPlan->isNot(TeamPlan::Frozen());
    }

    public function hasExceededMaximumHostsForPlan($maxHosts = null): bool
    {
        return $this->hosts()->active()->count() > ($maxHosts ?: $this->max_hosts);
    }

    /**
     * @return HasMany|Host
     */
    public function hosts()
    {
        return $this->hasMany(Host::class);
    }

    public function purgeableEvents(): array
    {
        return [
            'created' => 'team-'.$this->id,
            'updated' => 'team-'.$this->id,
            'deleted' => 'team-'.$this->id,
        ];
    }

    public function getReportCacheTag(): string
    {
        return "report_{$this->id}";
    }

    protected function shouldLogActivity(string $eventName): bool
    {
        return collect([
            'default_frontman_id',
            'timezone',
        ])->first(fn ($modelKey) => $this->wasChanged($modelKey), false);
    }

    protected function setActivityLogAction(string $eventName): string
    {
        switch (true) {
            case $this->wasChanged('default_frontman_id'):
                $location = Frontman::find($this->default_frontman_id)->location;

                return sprintf("Default check location set to %s.", $location);
            case $this->wasChanged('timezone'):
                return sprintf("Timezone set to %s.", $this->timezone);
        }
    }
}
