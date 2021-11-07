<?php

namespace App\Http\Resources;

use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection as LaravelAnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class AnonymousResourceCollection extends LaravelAnonymousResourceCollection
{
    public function transformToArray($request = null)
    {
        return $this->toResponse(
            $request ?: Container::getInstance()->make('request')
        )->getData(true);
    }

    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginatedResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }

    public function setMeta(array $array)
    {
        return $this->additional(array_merge_recursive([
            'meta' => $array,
        ], $this->additional));
    }

    /**
     * This will add a content array to the meta response which is used by the
     * frontend for UI and informational purposes.
     *
     * @param  array  $content
     * @return $this
     */
    public function addContentMeta(array $content)
    {
        return $this->setMeta(compact('content'));
    }
}
