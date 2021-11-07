<?php

namespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Http\Queries\HostActivityQuery;
use App\Http\Requests\Activity\HostActivityRequest;
use App\Http\Resources\Activity\HostActivityResource;
use App\Models\HostHistory;
use Illuminate\Support\Str;

class HostActivityController extends Controller
{
    public function __invoke(HostActivityRequest $request)
    {
        $this->authorize('viewAny', HostHistory::class);

        if ($this->useFakeData()) {
            $data = $this->makeFakeDataForTrialTeam($request);
        } else {
            $data = (new HostActivityQuery)->jsonPaginate();
        }

        return HostActivityResource::collection($data)
            ->addContentMeta($this->addContentMeta($request));
    }

    private function useFakeData()
    {
        return $this->team()->isOnTrial();
    }

    private function makeFakeDataForTrialTeam(HostActivityRequest $request)
    {
        $getDate = function () use ($request) {
            $date = $request->getMonthFilter()->startOfMonth();
            if (! $date->isCurrentMonth()) {
                $date = $date->addDays(random_int(1, 27));
            }

            return $date;
        };

        return collect(range(1, 10))->map(function () use ($getDate) {
            return new HostHistory([
                'host_id'    => Str::uuid()->toString(),
                'team_id'    => $this->team()->id,
                'user_id'    => $this->user()->id,
                'name'       => 'DEMO HOST DATA',
                'paid'       => true,
                'updated_at' => $date = $getDate(),
                'created_at' => $date,
            ]);
        });
    }

    private function addContentMeta(HostActivityRequest $request): array
    {
        $meta = [];
        if (optional($this->team()->upgraded_at)->isSameMonth($request->getMonthFilter())) {
            $upgradedAt = format_carbon_to_team_format($this->team(), $this->team()->upgraded_at);

            $meta = [
                'info' => "The table shows activities and prices since your account upgrade on {$upgradedAt}",
            ];
        }

        if ($this->useFakeData()) {
            $meta = array_merge_recursive($meta, [
                'info' => 'You are currently on a trial. The host activity data displayed is dummy data for illustrative purposes only. Your actual host activity will appear here after you upgrade.',
            ]);
        }

        return $meta;
    }
}
