<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\CustomCheck;

/**
 * @mixin CustomCheck
 */
class CustomCheckResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                     => $this->id,
            'hostId'                 => $this->host_id,
            'userId'                 => $this->user_id,
            'name'                   => $this->name,
            'token'                  => $this->token,
            'expectedUpdateInterval' => $this->expected_update_interval,
            'lastSuccess'            => $this->last_success,
            'dates'                  => [
                'lastCheckedAt' => DateTransformer::transform($this->last_checked_at),
                'updatedAt'     => DateTransformer::transform($this->updated_at),
                'createdAt'     => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
