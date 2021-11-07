<?php

namespace App\Models;

use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\Models\Activity;

/**
 * @mixin IdeHelperActivityLog
 */
class ActivityLog extends Activity implements ActivityContract
{
    use OwnedByTeam, HasFactory;
}
