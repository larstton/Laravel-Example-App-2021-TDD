<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventCollection extends ResourceCollection
{
    public static $wrap = null;

    public $collects = EventResource::class;

    public function toArray($request)
    {
        return [
            'events' => $this->collection,
        ];
    }
}
