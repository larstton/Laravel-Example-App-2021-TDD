<?php

namespace App\Http\Resources;

use App\Models\StatusPage;

/**
 * @mixin StatusPage
 */
class StatusPageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'teamId'           => $this->team_id,
            'meta'             => $this->meta,
            'token'            => $this->token,
            'url'              => $this->buildStatusPageUrl(),
            'imageContentType' => $this->image_content_type,
        ];
    }
}
