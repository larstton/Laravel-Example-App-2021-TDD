<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HostCollection extends ResourceCollection
{
    public static $wrap = null;

    public $collects = HostResource::class;

    public function toArray($request)
    {
        return [
            'hosts' => $this->collection,
        ];
    }
}
