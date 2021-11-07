<?php

namespace App\Models;

use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPaidMessageLog
 */
class PaidMessageLog extends BaseModel
{
    use OwnedByTeam;

    const UPDATED_AT = null;
    protected $table = 'paid_message_log';

    /**
     * @return BelongsTo|Recipient
     */
    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
