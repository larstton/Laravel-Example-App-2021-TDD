<?php

namespace App\Http\Resources\Frontman;

use App\Http\Resources\JsonResource;
use App\Models\Frontman;

/**
 * @mixin Frontman
 */
class FrontmanSummaryListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'location' => $this->location,
        ];
    }
}
