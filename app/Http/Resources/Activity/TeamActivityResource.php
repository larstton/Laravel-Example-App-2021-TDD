<?php

namespace App\Http\Resources\Activity;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\ActivityLog;

/**
 * @mixin ActivityLog
 */
class TeamActivityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'action'   => $this->description,
            'email'    => optional($this->causer)->email,
            'causedId' => optional($this->causer)->id,
            'dates'    => [
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
