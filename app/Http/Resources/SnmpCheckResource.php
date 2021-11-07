<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\SnmpCheck;

/**
 * @mixin SnmpCheck
 */
class SnmpCheckResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'hostId'        => $this->host_id,
            'userId'        => $this->user_id,
            'active'        => $this->active,
            'preset'        => $this->preset,
            'checkInterval' => $this->check_interval,
            'lastMessage'   => $this->last_message,
            'lastSuccess'   => $this->last_success,
            'dates'         => [
                'lastCheckedAt' => DateTransformer::transform($this->last_checked_at),
                'updatedAt'     => DateTransformer::transform($this->updated_at),
                'createdAt'     => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
