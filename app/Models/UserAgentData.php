<?php

namespace App\Models;

use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAgentData extends Model
{
    use OwnedByTeam, HasFactory;

    const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
