<?php

namespace App\Actions\Report;

use App\Models\Event;
use App\Models\HostHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CreateEventReportAction
{
    private User $user;

    public function execute(User $user, Carbon $from, Carbon $to, $filters = []): Collection
    {
        $this->user = $user;

        return $this
            ->getReport($from, $to, $filters)
            ->sortByDesc('created_at')
            ->values();
    }

    private function getReport(Carbon $from, Carbon $to, array $filters): Collection
    {
        return Cache::tags($this->user->team->getReportCacheTag())->remember(
            md5($this->user->id.$from->timestamp.$to->timestamp.json_encode($filters)),
            now()->addHour(),
            fn () => $this->buildReport($from, $to, $filters)
        );
    }

    private function buildReport(Carbon $from, Carbon $to, array $filters): Collection
    {
        $hostHistory = $this->getHostHistory($from, $to, $filters);
        $events = $this->getEvents($from, $to, $filters);

        $report = $hostHistory->mapWithKeys(function (HostHistory $hostHistory) {
            return [
                $hostHistory->host_id => [
                    'name'       => $hostHistory->name,
                    'issues'     => [],
                    'created_at' => $hostHistory->created_at->timestamp,
                ],
            ];
        })->all();

        $events->each(function (Event $event) use (&$report, $to, $from) {
            // Gets the high of the event created at date, or the requested start date for report.
            $start = max($event->created_at->timestamp, $from->timestamp);
            if (blank($event->resolved_at)) {
                $event->resolved_at = $to;
            }
            $stop = min($event->resolved_at->timestamp, $to->timestamp);

            $duration = $stop - $start;

            $hostId = $event->affected_host_id;
            $hostName = $event->meta->get('affectedHost.name');

            $triggerName = $event->meta['name'];

            if (! isset($report[$hostId])) {
                $report[$hostId] = [
                    'issues'     => [],
                    'created_at' => $event->created_at->timestamp,
                ];
            }

            if (! isset($report[$hostId]['name'])) {
                $report[$hostId]['name'] = $hostName;
            }

            $percent = round(100 * $duration / ($to->timestamp - $from->timestamp), 2);

            if (! isset($report[$hostId]['issues'][$triggerName])) {
                $report[$hostId]['issues'][$triggerName] = [
                    'name'       => $hostName,
                    'check'      => $triggerName,
                    'uuid'       => $event->id,
                    'issue'      => $triggerName,
                    'severity'   => $event['meta']['severity'],
                    'time'       => $duration,
                    'percent'    => $percent,
                    'created_at' => $event->created_at->timestamp,
                ];
            } else {
                $report[$hostId]['issues'][$triggerName]['time'] += $duration;
                $report[$hostId]['issues'][$triggerName]['percent'] += $percent;
                $report[$hostId]['issues'][$triggerName]['percent'] = min(
                    $report[$hostId]['issues'][$triggerName]['percent'],
                    100
                );
                $report[$hostId]['issues'][$triggerName]['created_at'] = $event->created_at->timestamp;
            }
        });

        return collect($report)
            ->flatMap(function ($report, $hostId) {
                if (blank($report['issues'])) {
                    $report['issues'] = [
                        [
                            'name'       => $report['name'],
                            'issue'      => null,
                            'created_at' => $report['created_at'],
                        ],
                    ];
                }

                return collect($report['issues'])
                    ->values()
                    ->map(fn ($issue) => tap($issue, function (&$issue) use ($hostId) {
                        $issue['host_id'] = $hostId;
                    }))
                    ->all();
            });
    }

    private function getHostHistory(Carbon $from, Carbon $to, array $filters): EloquentCollection
    {
        return HostHistory::query()
            ->withTrashed()
            ->where(function (EloquentBuilder $query) {
                $query
                    ->whereNotNull('deleted_at')
                    ->orWhereHas('host');
            })
            ->whereCreatedOrDeletedBetweenDates($from, $to)
            ->when(
                filled($search = data_get($filters, 'search')),
                fn ($query) => $query->whereLike(['host_histories.name'], $search)
            )
            ->when(
                filled($hostId = data_get($filters, 'host')),
                fn ($query) => $query->where('host_id', $hostId)
            )
            ->latest()
            ->get();
    }

    private function getEvents(Carbon $from, Carbon $to, array $filters): EloquentCollection
    {
        return Event::query()
            ->from(DB::raw('`events` FORCE INDEX (events_team_id_created_at_resolved_at_index)'))
            ->with(['host', 'frontman', 'rule'])
            ->where(function (EloquentBuilder $query) {
                $query
                    ->whereHas('frontman')
                    ->orWhereHas('host');
            })
            ->when(
                filled($search = data_get($filters, 'search')),
                fn ($query) => $query->whereLike(['events.meta', 'host.name'], $search)
            )
            ->when(
                filled($hostId = data_get($filters, 'host')),
                fn ($query) => $query->where('host_id', $hostId)
            )
            ->whereStartedOrResolvedBetweenDates($from, $to)
            ->latest()
            ->get();
    }
}
