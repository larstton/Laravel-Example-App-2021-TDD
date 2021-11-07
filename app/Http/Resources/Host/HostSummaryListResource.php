<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\JsonResource;
use App\Models\Host;

/**
 * @mixin Host
 */
class HostSummaryListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'connect' => $this->connect,
        ];
    }
}
