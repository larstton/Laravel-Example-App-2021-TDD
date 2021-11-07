<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Event;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    public static $wrap = 'event';

    public function toArray($request)
    {
        return [
            'uuid'             => $this->id,
            'checkKey'         => $this->check_key,
            'action'           => $this->action,
            'state'            => $this->state->value,
            'reminders'        => (bool) $this->reminders->value,
            'resolveTimestamp' => optional($this->resolved_at)->timestamp,
            'createTimestamp'  => optional($this->created_at)->timestamp,
            'meta'             => $this->meta,
            'lastCheckValue'   => $this->last_check_value ?? 0,
            'comments'         => EventCommentResource::collection($this->whenLoaded('eventComments')),
        ];
    }
}
