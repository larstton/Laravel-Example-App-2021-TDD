<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\ServiceCheck;
use Illuminate\Support\Str;

/**
 * @mixin ServiceCheck
 */
class ServiceCheckResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'hostId'        => $this->host_id,
            'userId'        => $this->user_id,
            'active'        => $this->active,
            'checkInterval' => $this->check_interval,
            'inProgress'    => $this->in_progress,
            'lastMessage'   => $this->last_message,
            'lastSuccess'   => $this->last_success,
            'port'          => $this->when(! $this->isIcmpCheck(), $this->port),
            'protocol'      => Str::upper($this->protocol),
            'service'       => Str::upper($this->service),
            'dates'         => [
                'lastCheckedAt' => DateTransformer::transform($this->last_checked_at),
                'updatedAt'     => DateTransformer::transform($this->updated_at),
                'createdAt'     => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
