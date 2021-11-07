<?php

namespace App\Http\Queries;

use App\Models\Host;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CustomCheckQuery extends QueryBuilder
{
    public function __construct(Host $host)
    {
        $query = $host->customChecks()
            ->getQuery()
            ->when(request()->has('search'), function ($query) {
                return $query->whereLike([
                    'name', 'token', 'expected_update_interval',
                ], request('search'));
            });

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
                AllowedSort::field('name'),
                AllowedSort::field('token'),
                AllowedSort::field('expected-update-interval', 'expected_update_interval'),
            ]);
    }
}
