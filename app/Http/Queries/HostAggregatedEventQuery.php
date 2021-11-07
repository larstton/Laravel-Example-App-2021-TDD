<?php

namespace App\Http\Queries;

use App\Models\Event;
use App\Models\Host;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class HostAggregatedEventQuery extends QueryBuilder
{
    private $withHostConstraints = null;

    public function __construct()
    {
        /** @var Event|QueryBuilder $this */
        $query = Event::query()
            ->whereActive()
            ->whereWarningOrAlert()
            ->orderByDesc('created_at')
            ->with('rule');

        parent::__construct($query);

        $this
            ->allowedFilters([
                AllowedFilter::callback('aggregate-entity', function (Builder $query, $value) {
                    $lookup = [
                        'group'    => function ($query) use ($value) {
                            $query->withAnyTags([$value], Host::getTagType());
                        },
                        'sub-unit' => function ($query) use ($value) {
                            $query->whereHas('subUnit', function ($query) use ($value) {
                                $query->where('sub_units.id', $value);
                            });
                        },
                    ];

                    $this->withHostConstraints[] = $lookup[request()->route('aggregateBy')];
                }),
                AllowedFilter::callback('group', function (Builder $query, $value) {
                    $this->withHostConstraints[] = function ($query) use ($value) {
                        $query->whereHas('tags', function (Builder $query) use ($value) {
                            $query->whereTagNameBeginsWith($value);
                        });
                    };
                }),
                AllowedFilter::callback('sub-unit', function (Builder $query, $value) {
                    $this->withHostConstraints[] = function ($query) use ($value) {
                        $query->whereHas('subUnit', function (Builder $query) use ($value) {
                            $query->where('sub_units.id', $value);
                        });
                    };
                }),
                AllowedFilter::callback('tag', function (Builder $query, $value) {
                    $this->withHostConstraints[] = function ($query) use ($value) {
                        call_user_func_array([$query, 'whereHasAnyTags'], Arr::wrap($value));
                    };
                }),
                AllowedFilter::callback('with-issues-only', function (Builder $query, $value) {
                    $this->withHostConstraints[] = function ($query) use ($value) {
                        $query->whereHasActiveEvents($value);
                    };
                }),
            ]);

        $this->withHostConstraints[] = function ($query) {
            $query
                ->whereScopedByUserHostTag(current_user())
                ->whereScopedByUserSubUnit(current_user());
        };

        if ($this->withHostConstraints) {
            $this->whereHas('host', function ($query) {
                collect($this->withHostConstraints)
                    ->each(function (callable $storedConstraint) use ($query) {
                        $storedConstraint($query);
                    });
            });
        }
    }
}
