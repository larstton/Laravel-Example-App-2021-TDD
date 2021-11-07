<?php

namespace App\Http\Resources\Host;

use App\Http\Resources\Frontman\HostFrontmanResource;
use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\Host;

/**
 * @mixin Host
 */
class FrontmanHostResource extends JsonResource
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
