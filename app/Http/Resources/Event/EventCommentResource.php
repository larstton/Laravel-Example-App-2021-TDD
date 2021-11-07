<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\EventComment;

/**
 * @mixin EventComment
 */
class EventCommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'eventId'  => $this->event_id,
            'teamId'   => $this->team_id,
            'userId'   => $this->user_id,
            'nickname' => $this->nickname,
            'text'     => $this->text,
            'actions'  => [
                'visibleToGuests' => $this->visible_to_guests,
                'statuspage'      => $this->statuspage,
                'forward'         => $this->forward,
            ],
            'dates'    => [
                'createdAt' => DateTransformer::transform($this->created_at),
                'updatedAt' => DateTransformer::transform($this->updated_at),
            ],
        ];
    }
}
