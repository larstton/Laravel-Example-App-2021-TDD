<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperReminder
 */
class Reminder extends Model
{
    use HasFactory;

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    protected $guarded = [];

    /**
     * @return BelongsTo|Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo|Recipient
     */
    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
