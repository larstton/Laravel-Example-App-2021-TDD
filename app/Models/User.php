<?php

namespace App\Models;

use App\Enums\TeamMemberRole;
use App\Enums\TeamPlan;
use App\Enums\TeamStatus;
use App\Models\Concerns\AuthedEntity;
use App\Models\Concerns\HasJWT;
use App\Models\Concerns\LogsActivity;
use App\Notifications\Auth\NewUserEmailVerificationNotification;
use App\Notifications\Auth\ResetPasswordUserNotification;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\CarbonInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Traits\CausesActivity;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @mixin IdeHelperUser
 */
class User extends BaseModel implements
    JWTSubject,
    AuthedEntity,
    MustVerifyEmailContract,
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use CastsEnums, Authenticatable, HasJWT, Authorizable, CanResetPassword, MustVerifyEmail, Notifiable, CausesActivity, LogsActivity;

    protected static $recordEvents = ['created'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $enumCasts = [
        'team_status' => TeamStatus::class,
        'role'        => TeamMemberRole::class,
    ];
    protected $casts = [
        'product_news' => 'bool',
    ];
    protected $dates = [
        'email_verified_at',
        'trial_end',
    ];
    protected $with = ['team'];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * @return BelongsTo|Team
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo|SubUnit
     */
    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordUserNotification($token, $this->email));
    }

    /**
     * @return HasMany|UserSetting
     */
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function sendEmailVerificationNotification()
    {
        if (! $this->hasVerifiedEmail()) {
            $this->notify(new NewUserEmailVerificationNotification);
        }
    }

    public function isTeamAdmin(): bool
    {
        return $this->role->is(TeamMemberRole::Admin());
    }

    public function isGuest(): bool
    {
        return $this->role->is(TeamMemberRole::Guest());
    }

    public function isTeamMember(): bool
    {
        return $this->role->in([TeamMemberRole::Member(), TeamMemberRole::Admin()]);
    }

    public function scopeRegisteredInPeriod(Builder $query, CarbonInterface $start, $period)
    {
        $from = new Carbon();
        $from->setDateTimeFrom($start)->subSeconds($period);
        $to = $start;

        $query->whereBetween('created_at', [$from->startOfHour(), $to->startOfHour()]);
    }

    public function scopeNotDeleted(Builder $query)
    {
        $query->where('team_status', '!=', TeamStatus::Deleted());
    }

    public function scopeSubscribedToProductNews(Builder $query)
    {
        $query->where('product_news', true);
    }

    public function scopeFromActiveTeam(Builder $query)
    {
        $query->whereHas('team', function (Builder $query) {
            $query->where('plan', '!=', TeamPlan::Frozen());
        });
    }

    public function scopeFromTeamWithoutHosts(Builder $query)
    {
        $query->whereHas('team', function (Builder $query) {
            $query->whereDoesntHave('hosts');
        });
    }

    public function scopeFromTeamWithHosts(Builder $query)
    {
        $query->whereHas('team', function (Builder $query) {
            $query->whereHas('hosts');
        });
    }

    public function scopeNotVerified(Builder $query)
    {
        $query->whereNull('email_verified_at');
    }

    public function scopeVerified(Builder $query)
    {
        $query->whereNotNull('email_verified_at');
    }

    public function scopePlanEndsBetween(Builder $query, $plan, CarbonInterface $start, $period)
    {
        $query->whereHas('team', function (Builder $query) use ($plan, $start, $period) {
            $from = new Carbon();
            $from->setDateTimeFrom($start)->subSeconds($period);
            $till = $start;

            $query->where('plan', $plan)
                ->whereBetween('trial_ends_at', [$from, $till]);
        });
    }

    public function scopeForMarketingReminders(Builder $query)
    {
        $query
            ->verified()
            ->notDeleted()
            ->subscribedToProductNews()
            ->fromActiveTeam();
    }

    public function scopeRegularUser(Builder $query)
    {
        $query
            ->verified()
            ->notDeleted()
            ->fromActiveTeam();
    }

    public function scopeSupportUser(Builder $query)
    {
        $query->where('email', 'regexp', 'support\+[0-9]+@cloudradar.co');
    }

    public function scopeUnverifiedUsers(Builder $query, $hours)
    {
        $query->notVerified()
            ->registeredInPeriod(
                now()->subHours($hours),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )
            ->notDeleted()
            ->fromActiveTeam();
    }

    public function scopeWithoutHosts(Builder $query)
    {
        $query->whereHas('team', function (Builder $query) {
            $query->whereDoesntHave('hosts');
        });
    }

    public function scopeWithHostsCreatedWithoutChecks(Builder $query, CarbonInterface $start, $period)
    {
        $query->hostsCreatedInPeriodWithoutChecks(false, $start, $period);
    }

    public function scopeWithCagentHostsCreatedWithoutChecks(Builder $query, CarbonInterface $start, $period)
    {
        $query->hostsCreatedInPeriodWithoutChecks(true, $start, $period);
    }

    public function scopeVerifiedInPeriod(Builder $query, CarbonInterface $start, $period)
    {
        $from = new Carbon();
        $from->setDateTimeFrom($start)->subSeconds($period);
        $till = $start;

        $query->whereBetween('email_verified_at', [$from, $till]);
    }

    protected function scopeHostsCreatedInPeriodWithoutChecks(
        Builder $query,
        bool $cagent,
        CarbonInterface $start,
        $period
    ) {
        $query->with(['team.hosts' => function ($builder) use ($start, $period, $cagent) {
            $from = new Carbon();
            $from->setDateTimeFrom($start)->subSeconds($period);
            $till = $start;

            $builder
                ->withoutGlobalScopes()
                ->whereDoesntHave('webChecks')
                ->whereDoesntHave('serviceChecks')
                ->whereDoesntHave('snmpChecks')
                ->whereDoesntHave('customChecks')
                ->whereBetween('created_at', [$from, $till])
                ->where('cagent', $cagent);

        }
        ])->whereHas('team', function (Builder $query) use ($start, $period, $cagent) {
            $query->whereHas('hosts', function (Builder $query) use ($start, $period, $cagent) {
                $from = new Carbon();
                $from->setDateTimeFrom($start)->subSeconds($period);
                $till = $start;

                $query
                    ->withoutGlobalScopes()
                    ->whereDoesntHave('webChecks')
                    ->whereDoesntHave('serviceChecks')
                    ->whereDoesntHave('snmpChecks')
                    ->whereDoesntHave('customChecks')
                    ->whereBetween('created_at', [$from, $till])
                    ->where('cagent', $cagent);
            });
        });
    }

    protected function scopeWithFrontmanHostsWithoutChecks(Builder $query, CarbonInterface $start, $period)
    {
        $query->with([
            'team.frontmen' => function ($builder) use ($start, $period) {
                $from = new Carbon();
                $from->setDateTimeFrom($start)->subSeconds($period);
                $till = $start;

                $builder
                    ->withoutGlobalScopes()
                    ->whereBetween('created_at', [$from, $till])
                    ->whereNull('last_heartbeat_at');
            },
        ])
            ->whereHas('team', function (Builder $query) use ($start, $period) {
                $query->whereHas('frontmen', function (Builder $query) use ($start, $period) {
                    $from = new Carbon();
                    $from->setDateTimeFrom($start)->subSeconds($period);
                    $till = $start;

                    $query
                        ->withoutGlobalScopes()
                        ->whereBetween('created_at', [$from, $till])
                        ->whereNull('last_heartbeat_at');
                });
            });;
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return "Account {$this->email} {$eventName}";
    }
}
