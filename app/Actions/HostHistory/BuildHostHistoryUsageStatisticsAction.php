<?php

namespace App\Actions\HostHistory;

use App\Models\HostHistory;
use Carbon\Carbon;

class BuildHostHistoryUsageStatisticsAction
{
    public function execute(Carbon $month): array
    {
        return HostHistory::query()
            ->withTrashed()
            ->whereIsPaid()
            ->whereInGivenMonth($month)
            ->get()
            ->reduce(function ($carry, HostHistory $hostHistory) use ($month) {
                $paidDuration = $hostHistory->getPaidDurationForPeriod($month);

                if ($paidDuration === 28) {
                    $carry['months'] += 1;
                } else {
                    $carry['days'] += $paidDuration;
                }

                return $carry;
            }, [
                'days'   => 0,
                'months' => 0,
            ]);
    }
}
