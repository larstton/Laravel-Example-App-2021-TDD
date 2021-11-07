<?php

namespace App\Http\Queries;

use App\Models\Host;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SNMPCheckQuery extends QueryBuilder
{
    public function __construct(Host $host)
    {
        $query = $host->snmpChecks()
            ->getQuery()
            ->when(request()->has('search'), function ($query) {
                return $query->whereLike([
                    'check_interval', 'preset',
                ], request('search'));
            });

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
                AllowedSort::field('preset'),
                AllowedSort::field('check-interval', 'check_interval'),
            ]);
    }
}
