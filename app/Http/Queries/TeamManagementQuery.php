<?php

namespace App\Http\Queries;

use App\Models\Team;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TeamManagementQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = Team::query();

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
            ])
            ->allowedFilters([
                AllowedFilter::exact('plan', 'teams.plan'),
            ]);
    }
}
