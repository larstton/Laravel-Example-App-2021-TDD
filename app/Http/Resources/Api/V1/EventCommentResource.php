<?php

namespace App\Http\Resources\Api\V1;

use App\Models\EventComment;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventComment
 */
class EventCommentResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'uuid'            => $this->id,
            'eventUuid'       => $this->event_id,
            'teamUuid'        => $this->team_id,
            'createdByUuid'   => $this->user_id,
            'createTimestamp' => optional($this->created_at)->timestamp,
            'nickname'        => $this->nickname,
            'text'            => $this->text,
            'guests'          => $this->visible_to_guests,
            'statuspage'      => $this->statuspage,
        ];
    }
}
