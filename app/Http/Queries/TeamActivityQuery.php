<?php

namespace App\Http\Queries;

use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TeamActivityQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = ActivityLog::query();

        parent::__construct($query);

        $from = $this->request->filter['from'];
        $to = $this->request->filter['to'];
        $this->request->merge([
            'filter' => [
                'between' => "{$from}:{$to}",
            ],
        ]);

        $this
            ->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'activity_log.created_at'),
            ])
            ->allowedFilters([
                AllowedFilter::callback('between', function ($query, $value) {
                    [$from, $to] = explode(':', $value);
                    $query->whereBetween('activity_log.created_at', [
                        Carbon::createFromTimestamp($from),
                        Carbon::createFromTimestamp($to),
                    ]);
                }),
            ]);
    }
}
