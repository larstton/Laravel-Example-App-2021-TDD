<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\ApiToken;

/**
 * @mixin ApiToken
 */
class ApiTokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'token'      => $this->token,
            'capability' => $this->capability->value,
            'dates'      => [
                'lastUsedAt' => DateTransformer::transform($this->last_used_at),
                'createdAt'  => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
