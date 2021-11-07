<?php

namespace App\Http\Queries;

use App\Models\Host;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ServiceCheckQuery extends QueryBuilder
{
    public function __construct(Host $host)
    {
        $query = $host->serviceChecks()
            ->getQuery()
            ->when(request()->has('search'), function ($query) {
                return $query->whereLike([
                    'service', 'protocol', 'port', 'check_interval',
                ], request('search'));
            });

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
                AllowedSort::field('service'),
                AllowedSort::field('protocol'),
                AllowedSort::field('port'),
                AllowedSort::field('check-interval', 'check_interval'),
            ]);
    }
}
