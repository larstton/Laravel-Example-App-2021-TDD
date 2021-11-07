<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse as LaravelPaginatedResourceResponse;
use Illuminate\Support\Arr;

class PaginatedResourceResponse extends LaravelPaginatedResourceResponse
{
    protected function paginationInformation($request)
    {
        return [
            'meta' => $this->meta($this->resource->resource->toArray()),
        ];
    }

    protected function meta($paginated)
    {
        $meta = [
            'pagination' => Arr::except($paginated, [
                'data',
                'path',
                'from',
                'to',
                'per_page',
                'current_page',
                'last_page',
                'first_page_url',
                'last_page_url',
                'prev_page_url',
                'next_page_url',
            ]),
        ];
        $meta['pagination']['totalPages'] = $paginated['last_page'];
        $meta['pagination']['perPage'] = $paginated['per_page'];
        $meta['pagination']['currentPage'] = $paginated['current_page'];
        $meta['pagination']['count'] = $paginated['total'];
        $meta['pagination']['links'] = $this->paginationLinks($paginated);

        return $meta;
    }
}
