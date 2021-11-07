<?php

namespace App\Models;

use App\Enums\SupportRequestState;
use App\Models\Concerns\OwnedByTeam;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSupportRequest
 */
class SupportRequest extends BaseModel
{
    use OwnedByTeam, CastsEnums;

    protected $casts = [
        'attachment' => 'array',
    ];

    protected $enumCasts = [
        'state' => SupportRequestState::class,
    ];

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
