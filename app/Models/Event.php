<?php

namespace App\Models;

use App\Enums\EventAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Events\Event\EventCreated;
use App\Events\Event\EventDeleted;
use App\Events\Event\EventUpdated;
use App\Models\Concerns\HasAssociatedChecks;
use App\Models\Concerns\HasMeta;
use App\Models\Concerns\OwnedByTeam;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperEvent
 */
class Event extends BaseModel
{
    use OwnedByTeam, CastsEnums, HasAssociatedChecks, HasMeta;

    protected $dispatchesEvents = [
        'created' => EventCreated::class,
        'updated' => EventUpdated::class,
        'deleted' => EventDeleted::class,
    ];

    protected $dates = [
        'resolved_at',
        'last_checked_at',
    ];

    protected $casts = [
        'state'     => 'int',
        'reminders' => 'int',
        'meta'      => 'json',
    ];

    protected $enumCasts = [
        'action'    => EventAction::class,
        'state'     => EventState::class,
        'reminders' => EventReminder::class,
    ];

    /**
     * @return BelongsTo|Host
     */
    public function host()
    {
        return $this->belongsTo(Host::class)
            ->whereScopedByUserHostTag(current_user())
            ->whereScopedByUserSubUnit(current_user());
    }

    /**
     * @return BelongsTo|Frontman
     */
    public function frontman()
    {
        return $this->belongsTo(Frontman::class, 'host_id', 'id');
    }

    /**
     * @return BelongsTo|Rule
     */
    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }

    /**
     * @return HasMany|EventComment
     */
    public function eventComments()
    {
        return $this->hasMany(EventComment::class);
    }

    /**
     * @return HasMany|Reminder
     */
    public function sentReminders()
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * @return HasMany|EventComment
     */
    public function guestOnlyComments()
    {
        return $this->hasMany(EventComment::class)
            ->where('visible_to_guests', true);
    }

    public function scopeWhereActiveEventForHostAndJobId(Builder $query, Host $host, $jobId)
    {
        return $query->whereHostId($host->id)
            ->whereCheckKey("jobmon:{$jobId}")
            ->whereCheckId($host->id)
            ->whereActive();
    }

    public function scopeWhereActive(Builder $query)
    {
        $query->where('events.state', EventState::Active());
    }

    public function scopeWhereWarningOrAlert(Builder $query)
    {
        $query->whereIn('events.action', [
            EventAction::Warning(),
            EventAction::Alert(),
        ]);
    }

    public function scopeWhereOnOrAfter(Builder $query, \Carbon\Carbon $carbon)
    {
        $query->whereDate('events.created_at', '>=', $carbon);
    }

    public function scopeWhereAgentEvent(Builder $query)
    {
        $query->where('is_agent_event', true);
    }

    public function scopeWhereStartedOrResolvedBetweenDates(Builder $query, Carbon $from, Carbon $to)
    {
        $query
            ->where(function ($query) use ($to, $from) {
                /* started before timeframe and ended after timeframe start (and even timeframe end) */
                $query->where('created_at', '<=', $from)
                    ->where('resolved_at', '>=', $from);
            })
            ->orWhere(function ($query) use ($to, $from) {
                /* started in timeframe (including ended in timeframe) */
                $query->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to);
            })
            ->orWhere(function ($query) use ($to) {
                /* events not resolved by end of period*/
                $query->where('resolved_at', null)
                    ->where('created_at', '<=', $to);
            })
            ->orWhere(function ($query) use ($from, $to) {
                /* events resolved in the timeframe */
                $query->where('resolved_at', '<=', $to)
                    ->where('resolved_at', '>=', $from);
            });
    }

    public function scopeState(Builder $query, ...$states): Builder
    {
        if (is_string($states) && Str::of($states)->trim()->lower()->is('all')) {
            return $query;
        }

        $states = collect(Arr::wrap($states))->map(function ($state) {
            if (is_string($state)) {
                $state = EventState::coerce($state);
            }

            return $state;
        })->reject(fn ($state) => is_null($state));

        if ($states->isEmpty()) {
            return $query;
        }

        return $query->whereIn('events.state', $states);
    }
}
