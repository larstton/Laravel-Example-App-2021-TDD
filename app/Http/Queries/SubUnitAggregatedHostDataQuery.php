<?php

namespace App\Http\Queries;

use App\Enums\EventAction;
use App\Enums\EventState;
use App\Models\SubUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubUnitAggregatedHostDataQuery extends QueryBuilder
{
    private $withHostRelationshipConstraints = null;

    public function __construct()
    {
        /** @var SubUnit|QueryBuilder $this */
        $query = SubUnit::query()
            ->whereHas('hosts')
            ->withCount('hosts')
            ->with([
                'hosts.webChecks',
                'hosts.snmpChecks',
                'hosts.customChecks',
                'hosts.serviceChecks',
            ]);

        parent::__construct($query);

        $this
            ->allowedFilters([
                AllowedFilter::callback('group', function (Builder $query, $value) {
                    $this->withHostRelationshipConstraints[] = function ($query) use ($value) {
                        $query->whereHas('tags', function (Builder $query) use ($value) {
                            $query->whereTagNameBeginsWith($value);
                        });
                    };
                }),
                AllowedFilter::callback('sub-unit', function (Builder $query, $value) {
                    $this->withHostRelationshipConstraints[] = function ($query) use ($value) {
                        $query->whereHas('subUnit', function (Builder $query) use ($value) {
                            $query->where('sub_units.id', $value);
                        });
                    };
                }),
                AllowedFilter::callback('tag', function (Builder $query, $value) {
                    $this->withHostRelationshipConstraints[] = function ($query) use ($value) {
                        call_user_func_array([$query, 'whereHasAnyTags'], Arr::wrap($value));
                    };
                }),
                AllowedFilter::callback('with-issues-only', function (Builder $query, $value) {
                    $this->withHostRelationshipConstraints[] = function ($query) use ($value) {
                        $query->whereHasActiveEvents($value);
                    };
                }),
            ]);

        $this->withHostRelationshipConstraints[] = function ($query) {
            $query
                ->select('hosts.*')
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
                ->groupBy(['hosts.id']);
        };

        if ($this->withHostRelationshipConstraints) {
            $this->with([
                'hosts' => function ($query) {
                    collect($this->withHostRelationshipConstraints)
                        ->each(function (callable $storedConstraint) use ($query) {
                            $storedConstraint($query);
                        });
                },
            ]);
        }
    }
}
