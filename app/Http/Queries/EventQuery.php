<?php

namespace App\Http\Queries;

use App\Enums\EventState;
use App\Models\Event;
use App\Models\Host;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EventQuery extends QueryBuilder
{
    public function __construct()
    {
        $hosts = Host::query()
            ->whereScopedByUserHostTag(current_user())
            ->whereScopedByUserSubUnit(current_user());

        $query = Event::query()
            ->with(['host', 'rule'])
            ->selectRaw('events.*, IFNULL(`hosts`.`name`,`host_histories`.`name`) as `host_name`')
            ->joinSub($hosts, 'hosts', function ($join) {
                $join->on('hosts.id', 'events.host_id');
            })
            ->leftJoin('host_histories', function ($join) {
                $join->on('events.host_id', '=', 'host_histories.host_id')
                    ->where('host_histories.paid', '=', 0)
                    ->whereNotNull('host_histories.deleted_at');
            })
            ->when(filled(request('search')), function ($query) {
                return $query->whereLike([
                    'host.name', 'host.connect', 'host.description',
                    'meta', 'check_key',
                ], request('search'));
            });

        parent::__construct($query);

        if (filled(optional($this->request->filter)['from'])) {
            $from = $this->request->filter['from'];
            $to = $this->request->filter['to'];
            $this->request->merge([
                'filter' => [
                    'between' => "{$from}:{$to}",
                    'state'   => $this->request->filter['state'],
                ],
            ]);
        }

        $this->request->replace($this->request->except([
            'filter.from',
            'filter.to',
        ]));

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-last-checked', 'last_checked_at'),
                AllowedSort::field('host', 'hosts.name'),
            ])
            ->allowedFilters([
                AllowedFilter::scope('state')->default(EventState::Active),
                AllowedFilter::callback('between', function ($query, $value) {
                    [$from, $to] = explode(':', $value);
                    $query->whereBetween('events.created_at', [
                        Carbon::createFromTimestamp($from),
                        Carbon::createFromTimestamp($to),
                    ]);
                }),
            ]);
    }
}
