<?php

namespace App\Http\Queries;

use App\Enums\EventAction;
use App\Models\Host;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class HostQuery extends QueryBuilder
{
    public function __construct()
    {
        /** @var Host|QueryBuilder $this */
        $query = Host::query()
            ->with('tags')
            ->withCheckCount()
            ->withSnmpLastUpdatedAt()
            ->whereScopedByUserHostTag(current_user())
            ->whereScopedByUserSubUnit(current_user())
            ->when(filled(request('search')), function ($query) {
                return $query->whereLike([
                    'hosts.name', 'hosts.connect', 'hosts.description',
                ], request('search'));
            });

        parent::__construct($query);

        $allowedSorts = [
            AllowedSort::field('date-created', 'hosts.created_at'),
            AllowedSort::field('date-updated', 'hosts.updated_at'),
            AllowedSort::field('connect', 'hosts.connect'),
            AllowedSort::field('name', 'hosts.name'),
            AllowedSort::field('description', 'hosts.description'),
            AllowedSort::field('frontman','hosts.frontman_id'),
        ];

        if (Str::contains(request('append', ''), 'summary')) {
            $allowedSorts[] = AllowedSort::field('checks-count', 'check_count_total');
        }

        if (Str::contains(request('append', ''), 'events')) {
            $this->withActiveEventsConstrained([
                EventAction::Warning(),
                EventAction::Alert(),
            ], current_user()->isGuest());
        }

        $this
            ->defaultSort('-hosts.created_at')
            ->allowedSorts($allowedSorts)
            ->allowedAppends(['summary', 'events'])
            ->allowedIncludes([
                AllowedInclude::relationship('frontman'),
                AllowedInclude::relationship('sub-unit', 'subUnit'),
                AllowedInclude::count('web-check-count', 'webChecksCount'),
                AllowedInclude::count('service-check-count', 'serviceChecksCount'),
                AllowedInclude::count('custom-check-count', 'customChecksCount'),
                AllowedInclude::count('snmp-check-count', 'snmpChecksCount'),
            ])
            ->allowedFilters([
                AllowedFilter::scope('dashboard-only', 'onlyDashboardVisible'),
                AllowedFilter::scope('with-issues-only', 'whereHasActiveEvents'),
                AllowedFilter::exact('sub-unit', 'sub_unit_id'),
                AllowedFilter::scope('tag', 'whereHasAnyTags'),
                AllowedFilter::scope('group', 'whereHasAnyGroupTags'),
            ]);
    }
}
