<?php

namespace App\Http\Queries;

use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseQueryBuilder extends QueryBuilder
{
    public function jsonPaginate()
    {
        if (request('page.size') === '-1') {
            $clonedQuery = clone $this;
            request()->merge([
                'page' => [
                    'size'   => $clonedQuery->count(),
                    'number' => request('page.number'),
                ],
            ]);
        }

        return parent::jsonPaginate();
    }
}
