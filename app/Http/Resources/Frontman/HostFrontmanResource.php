<?php

namespace App\Http\Resources\Frontman;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Http\Transformers\JsonTransformer;
use App\Models\Frontman;
use App\Models\Host;

/**
 * @mixin Frontman
 */
class HostFrontmanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'type'            => $this->type,
            'location'        => $this->location,
            'lastHeartbeatAt' => DateTransformer::transform($this->last_heartbeat_at),
        ];
    }
}
