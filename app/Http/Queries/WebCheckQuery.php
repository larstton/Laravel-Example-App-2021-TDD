<?php

namespace App\Http\Queries;

use App\Models\Host;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class WebCheckQuery extends QueryBuilder
{
    public function __construct(Host $host)
    {
        $query = $host->webChecks()
            ->getQuery()
            ->when(request()->has('search'), function ($query) {
                return $query->whereLike([
                    'path', 'protocol', 'port', 'check_interval', 'expected_http_status', 'method'
                ], request('search'));
            });

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
                AllowedSort::field('expected-string', 'expected_pattern'),
                AllowedSort::field('url', 'path'),
                AllowedSort::field('check-interval', 'check_interval'),
            ]);
    }
}
