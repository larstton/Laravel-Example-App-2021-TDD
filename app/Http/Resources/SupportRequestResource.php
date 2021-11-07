<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\SupportRequest;

/**
 * @mixin SupportRequest
 */
class SupportRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'subject'    => $this->subject,
            'body'       => $this->body,
            'state'      => $this->state->value,
            'attachment' => $this->attachment,
            'dates'      => [
                'updatedAt' => DateTransformer::transform($this->updated_at),
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
