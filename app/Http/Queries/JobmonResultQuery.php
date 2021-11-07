<?php

namespace App\Http\Queries;

use App\Models\Host;
use App\Models\JobmonResult;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class JobmonResultQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = JobmonResult::query();

        parent::__construct($query);

        /** @var Host $host */
        $host = $this->request->route('host');
        $jobId = $this->request->route('jobId');

        $this->where('host_id', $host->id)
            ->where('job_id', $jobId);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
            ])
            ->allowedFilters([
                AllowedFilter::scope('date-range', 'whereBetweenDates'),
                AllowedFilter::callback('successful-only', function (Builder $query) {
                    return $query->where('data->exit_code', 0);
                }),
                AllowedFilter::callback('failed-only', function (Builder $query) {
                    return $query->where('data->exit_code', '>', 0);
                }),
            ]);
    }
}
