<?php

namespace App\Models;

use App\Events\Event\EventCommentCreated;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperEventComment
 */
class EventComment extends BaseModel
{
    use OwnedByTeam, LogsActivity;

    const UPDATED_AT = null;

    protected $dispatchesEvents = [
        'created' => EventCommentCreated::class,
    ];

    protected $casts = [
        'visible_to_guests' => 'bool',
        'statuspage'        => 'bool',
        'forward'           => 'bool',
    ];

    /**
     * @return BelongsTo|Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return "User {$this->nickname} {$eventName} comment on event \"{$this->event->meta->name}\"";
    }
}
