<?php

namespace App\Http\Queries;

use App\Enums\EventAction;
use App\Enums\EventState;
use App\Models\Host;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class HostEventQuery extends QueryBuilder
{
    public function __construct(Host $host)
    {
        $query = $host->events()
            ->whereActive()
            ->whereWarningOrAlert()
            ->orderByDesc('created_at')
            ->with('rule')
            ->getQuery();

        parent::__construct($query);
    }
}
