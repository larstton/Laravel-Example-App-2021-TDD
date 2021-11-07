<?php

namespace App\Http\Queries;

use App\Models\HostHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class HostActivityQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = HostHistory::query()->whereIsPaid();

        parent::__construct($query);

        $this
            ->withTrashed()
            ->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
            ])
            ->allowedFilters([
                AllowedFilter::scope('month', 'whereInGivenMonth')
                    ->default(date('Y-m')),
            ]);
    }
}
