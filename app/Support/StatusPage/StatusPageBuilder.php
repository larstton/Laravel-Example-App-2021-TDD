<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

namespace App\Support\StatusPage;

use App\Enums\EventAction;
use App\Enums\EventState;
use App\Http\Loophole\Resources\StatusPageResource;
use App\Models\Event;
use App\Models\Host;
use App\Models\StatusPage;
use App\Models\Tag;
use App\Models\Team;
use Carbon\CarbonPeriod;
use Closure;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StatusPageBuilder
{
    private StatusPage $statusPage;
    private Team $team;
    private ?Collection $hosts;
    private ?Collection $history;
    private bool $forHistory;
    private bool $forBadge;
    private bool $forShield;

    private function __construct(StatusPage $statusPage)
    {
        $this->statusPage = $statusPage;
        $this->team = $this->statusPage->team;
        $this->hosts = null;
        $this->history = null;
        $this->forHistory = false;
        $this->forBadge = false;
        $this->forShield = false;
    }

    public static function for(StatusPage $statusPage): self
    {
        return tap(new self($statusPage))->build();
    }

    public static function getBadgeFor(StatusPage $statusPage): self
    {
        return tap(new self($statusPage))->buildBadge();
    }

    public static function getShieldFor(StatusPage $statusPage): self
    {
        return tap(new self($statusPage))->buildShield();
    }

    public static function getHistoryFor(StatusPage $statusPage): self
    {
        return Cache::remember("status_page_history::{$statusPage->id}", now()->addMinute(),
            fn () => tap(new self($statusPage))->buildHistory()
        );
    }

    public function buildBadge()
    {
        $this->forBadge = true;

        $this->build();
    }

    public function build()
    {
        $this->hosts = $this->getHostList();
    }

    private function getHostList(): Collection
    {
        return Host::query()
            ->select('hosts.id', 'hosts.name')
            ->with('tags')
            ->selectRaw(
                'SUM(CASE WHEN `events`.`action` = ? THEN 1 ELSE 0 END) AS alert_count',
                [EventAction::Alert()]
            )
            ->selectRaw(
                'SUM(CASE WHEN `events`.`action` = ? THEN 1 ELSE 0 END) AS warning_count',
                [EventAction::Warning()]
            )
            ->leftJoin('events', function (JoinClause $join) {
                $join->on('events.host_id', '=', 'hosts.id')
                    ->where('events.state', '=', EventState::Active);
            })
            ->where('hosts.team_id', $this->team->id)
            ->active()
            ->when($this->getTagsToFilterHostsBy()->isNotEmpty(), $this->filterByTagsQuery())
            ->when($this->showHostsWithIssuesOnly(), function ($query) {
                $query->having('alert_count', '>', 0)
                    ->orHaving('warning_count', '>', 0);
            })
            ->groupBy('hosts.id')
            ->orderBy('hosts.name')
            ->get();
    }

    public function getTagsToFilterHostsBy(): Collection
    {
        return collect($this->statusPage->meta->get('hostTags', []));
    }

    private function filterByTagsQuery(): Closure
    {
        return function ($query) {
            $tagIds = $this->getTagsToFilterHostsBy()
                ->map(fn ($tag) => str_replace('*', '%', $tag))
                ->flatMap(fn ($tag) => Tag::containing($tag)
                    ->withType(Host::getTagType())
                    ->pluck('id'));

            if ($tagIds->isNotEmpty()) {
                $query->whereHas('tags', function ($query) use ($tagIds) {
                    $query->where(
                        fn ($query) => $query->whereIn('tags.id', $tagIds)
                    );
                });
            }
        };
    }

    public function showHostsWithIssuesOnly(): bool
    {
        return (bool) $this->statusPage->meta->get('hideOperational', false);
    }

    public function getEventCountGroupedByTag(): array
    {
        return Host::getTagListForActiveHosts()->pluck('name')
            ->filter(
                fn ($tag) => $this->getTagsToFilterHostsBy()->first(
                    fn ($tagPattern) => Str::is($tagPattern, $tag)
                )
            )->mapWithKeys(function ($tag) {
                $hostsByTag = $this->hosts->filter(function (Host $host) use ($tag) {
                    return in_array($tag, $host->tags->pluck('name')->all());
                });

                return [
                    $tag => [
                        'alerts'   => (int) $hostsByTag->sum->alert_count,
                        'warnings' => $this->includeWarnings()
                            ? (int) $hostsByTag->sum->warning_count
                            : new MissingValue,
                    ],
                ];
            })->toArray();
    }

    public function includeWarnings(): bool
    {
        return (bool) $this->statusPage->meta->get('showWarnings', false);
    }

    public function includeGroupedByTag(): bool
    {
        return (bool) $this->statusPage->meta->get('groupByTag', false);
    }

    public function buildShield()
    {
        $this->forShield = true;

        $this->build();
    }

    public function buildHistory()
    {
        $this->forHistory = true;

        if ($this->getNumberOfDaysForHistoricalData() === 0) {
            return $this->history = collect();
        }

        $events = Event::query()
            ->selectRaw('events.*, hosts.name  as host_name')
            ->whereOnOrAfter(now()->subDays($this->getNumberOfDaysForHistoricalData()))
            ->whereIn('host_id', $this->getHostIds())
            ->where(function ($query) {
                $query->where('action', EventAction::Alert())
                    ->when($this->includeWarnings(), function ($query) {
                        $query->orWhere('action', EventAction::Warning());
                    });
            })
            ->join('hosts', 'events.host_id', '=', 'hosts.id')
            ->with('eventComments')
            ->orderBy('events.created_at', 'DESC')
            ->get()
            ->map(function (Event $event) {
                $createdAt = $event->created_at->setTimezone($this->team->timezone);

                return [
                    'createdAt' => $createdAt->format('Y-m-d'),
                    'date'      => $createdAt->format('H:i:s'),
                    'state'     => $event->state->is(EventState::Active()) ? 'identified' : 'resolved',
                    'action'    => $event->action->value,
                    'event'     => $event->meta->get('name'),
                    'host'      => $event->host_name,
                    'comments'  => $event->eventComments
                        ->where('statuspage', true)
                        ->sortBy('created_at')
                        ->toArray(),
                ];
            })
            ->groupBy([
                fn ($event) => $event['createdAt'],
                fn ($event) => 'issues',
                fn ($event) => $event['host'],
            ]);

        $date = now()->subDays($this->getNumberOfDaysForHistoricalData());
        $startDate = $this->team->created_at->greaterThan($date)
            ? $this->team->created_at
            : $date;

        $period = CarbonPeriod::create(
            $startDate->setTimezone($this->team->timezone),
            now()->setTimezone($this->team->timezone)
        );

        $this->history = $this->insertMissingPeriods($events, $period);
    }

    public function getNumberOfDaysForHistoricalData(): int
    {
        return (int) $this->statusPage->meta->get('history', 0);
    }

    private function getHostIds(): Collection
    {
        return Host::query()
            ->select('hosts.id')
            ->where('hosts.team_id', $this->team->id)
            ->active()
            ->when($this->getTagsToFilterHostsBy()->isNotEmpty(), $this->filterByTagsQuery())
            ->orderBy('hosts.name')
            ->pluck('id');
    }

    private function insertMissingPeriods(Collection $data, CarbonPeriod $period): Collection
    {
        return collect($period)
            ->reverse()
            ->mapWithKeys(fn (Carbon $date) => [$date->format('Y-m-d') => ['issues' => []]])
            ->merge($data);
    }

    public function getStateForShield()
    {
        if ($this->getTotalAlertCount() > 0) {
            return 'error';
        }

        if ($this->getTotalWarningCount() > 0) {
            return 'warning';
        }

        return 'success';
    }

    public function getTotalAlertCount(): int
    {
        return $this->hosts->sum->alert_count;
    }

    public function getTotalWarningCount(): int
    {
        return $this->hosts->sum->warning_count;
    }

    public function getShieldMessageByState(Request $request, $state)
    {
        $messageLookup = [
            'success' => fn ($request) => $request->input('textSuccess', 'All OK'),
            'warning' => fn ($request) => $request->input('textWarning', 'OK With Warnings'),
            'error'   => fn ($request) => $request->input('textError', 'Non-Operational'),
        ];

        return $messageLookup[$state]($request);
    }

    public function getShieldColorByState(Request $request, $state)
    {
        $colorLookup = [
            'success' => fn ($request) => $request->input('colorSuccess', '#4CAF50'),
            'warning' => fn ($request) => $request->input('colorWarning', '#FFA000'),
            'error'   => fn ($request) => $request->input('colorError', '#C62828'),
        ];

        return $colorLookup[$state]($request);
    }

    public function hasHistory(): bool
    {
        return $this->forHistory;
    }

    public function hasBadge(): bool
    {
        return $this->forBadge;
    }

    public function hasShield(): bool
    {
        return $this->forShield;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function statusPageData(): array
    {
        return [
            'title'            => $this->getStatusPageTitle(),
            'header'           => $this->statusPage->meta->get('header'),
            'footer'           => $this->statusPage->meta->get('footer'),
            'imageContentType' => $this->statusPage->image_content_type,
            'history'          => $this->getNumberOfDaysForHistoricalData(),
            'settings'         => $this->statusPage->meta->get('settings'),
            'hideOperational'  => $this->statusPage->meta->get('hideOperational', false),
            'timezone'         => $this->team->timezone,
        ];
    }

    public function getStatusPageTitle()
    {
        return $this->statusPage->title;
    }

    public function getEventCountGroupedByHost(): array
    {
        return $this->hosts->mapWithKeys(function ($host) {
            return [
                $host->name => [
                    'alerts'   => (int) $host->alert_count,
                    'warnings' => $this->includeWarnings()
                        ? (int) $host->warning_count
                        : new MissingValue,
                ],
            ];
        })->toArray();
    }

    public function toResponse(): StatusPageResource
    {
        return StatusPageResource::make($this);
    }
}
