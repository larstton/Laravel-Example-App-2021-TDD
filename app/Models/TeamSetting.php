<?php

namespace App\Models;

use App\Models\Concerns\PurgesCache;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTeamSetting
 */
class TeamSetting extends Model
{
    use PurgesCache;

    protected $primaryKey = 'team_id';

    protected $casts = [
        'value' => 'array',
    ];

    protected $fillable = [
        'team_id',
        'value',
    ];

    public function purgeableEvents(): array
    {
        return [
            'created' => 'team-settings-'.$this->team_id,
            'updated' => 'team-settings-'.$this->team_id,
            'deleted' => 'team-settings-'.$this->team_id,
        ];
    }
}
