<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;
use Illuminate\Pagination\AbstractPaginator;

class JsonResource extends LaravelJsonResource
{
    /**
     * @param $resource
     * @return AnonymousResourceCollection
     */
    public static function collection($resource): AnonymousResourceCollection
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    /**
     * @param  array  $array
     * @return $this
     */
    public function setMeta(array $array)
    {
        $this->additional([
            'meta' => $array,
        ]);

        return $this;
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginatedResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }
}
