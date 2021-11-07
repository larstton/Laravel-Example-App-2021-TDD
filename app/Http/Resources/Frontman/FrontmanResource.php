<?php

namespace App\Http\Resources\Frontman;

use App\Http\Resources\Host\FrontmanHostResource;
use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Http\Transformers\JsonTransformer;
use App\Models\Frontman;

/**
 * @mixin Frontman
 */
class FrontmanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'location'  => $this->location,
            'version'   => $this->version,
            'hostInfo'  => JsonTransformer::makeKeysCamelCase($this->host_info),
            'hostCount' => $this->whenLoaded('filteredHosts', function () {
                return $this->filteredHosts->count();
            }),
            'hosts'     => FrontmanHostResource::collection($this->whenLoaded('filteredHosts')),
            'type'      => $this->type,
            'dates'     => [
                'lastHeartbeatAt' => DateTransformer::transform($this->last_heartbeat_at),
                'updatedAt'       => DateTransformer::transform($this->updated_at),
                'createdAt'       => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
