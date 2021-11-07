<?php

namespace App\Models;

use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @mixin IdeHelperHostHistory
 */
class HostHistory extends Model
{
    use SoftDeletes, OwnedByTeam, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'paid' => 'bool',
    ];

    public function scopeWhereNotPaid(Builder $query)
    {
        $query->where('paid', false);
    }

    public function scopeWhereIsPaid(Builder $query)
    {
        $query->where('paid', true);
    }

    public function scopeWhereInGivenMonth(Builder $query, $month)
    {
        $month = Carbon::parse($month);
        $query->whereCreatedOrDeletedBetweenDates(
            $month->copy()->startOfMonth(),
            $month->copy()->endOfMonth()
        );
    }

    public function scopeWhereCreatedOrDeletedBetweenDates(Builder $query, Carbon $from, Carbon $to)
    {
        $query
            ->where(function (Builder $query) use ($from, $to) {
                // Host was created and deleted inside of the given month:
                $query
                    // Where Host was created on or before the last day of month...
                    ->where('created_at', '<=', $to)
                    // ...and was deleted on or after the beginning of the month.
                    ->where('deleted_at', '>=', $from);
            })
            ->orWhere(function (Builder $query) use ($from, $to) {
                // Host was created inside of month and not deleted:
                $query
                    // Where Host was created on or before the last day of month...
                    ->where('created_at', '<=', $to)
                    // ...and is not deleted.
                    ->whereNull('deleted_at');
            });
    }

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
     * @return BelongsTo|User
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTotalPaidForPeriod(\Carbon\Carbon $period): float
    {
        $dailyRate = config('cloudradar.plans.payg.daily_rate.'.($this->team->currency ?? 'euro'));

        return round($this->getPaidDurationForPeriod($period) * $dailyRate, 2);
    }

    public function getPaidDurationForPeriod(\Carbon\Carbon $period): int
    {
        if (! $this->paid) {
            return 0;
        }

        $start = $this->getStartDateOfLog($period);
        $stop = $this->getEndDateOfLog($period);

        return min(28, $stop->startOfDay()->floatDiffInDays($start->startOfDay()) + 1);
    }

    /**
     * Start date is either the `created_at` date of the log entry, or the date
     * supplied as an arg to the method, whichever is greater.
     *
     * @param  \Carbon\Carbon  $startDate
     * @return \Carbon\Carbon
     */
    public function getStartDateOfLog(\Carbon\Carbon $startDate)
    {
        $startDate = $startDate->copy()->startOfMonth();

        return $this->created_at->isBefore($startDate)
            ? $startDate
            : $this->created_at;
    }

    /**
     * @param  \Carbon\Carbon  $endDate
     * @return \Carbon\Carbon|Carbon|null
     */
    public function getEndDateOfLog(\Carbon\Carbon $endDate)
    {
        $endDate = $endDate->copy()->endOfMonth();
        if ($this->trashed() && $this->deleted_at->isBefore($endDate)) {
            return $this->deleted_at;
        }

        return $endDate->isCurrentMonth() ? now() : $endDate;
    }

    public function getDurationForPeriod(\Carbon\Carbon $period): float
    {
        $start = $this->getStartDateOfLog($period);
        $stop = $this->getEndDateOfLog($period);

        return ceil($stop->floatDiffInDays($start));
    }
}
