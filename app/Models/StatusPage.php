<?php

namespace App\Models;

use App\Models\Concerns\HasMeta;
use App\Models\Concerns\HasUniqueToken;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;

/**
 * @mixin IdeHelperStatusPage
 */
class StatusPage extends BaseModel
{
    use OwnedByTeam, HasMeta, HasUniqueToken, LogsActivity;

    protected static $logAttributesToIgnore = ['image'];

    public function buildStatusPageUrl()
    {
        return route('web.status-pages.show', ['token' => $this->token]);
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return 'Status page with token '.$this->token.' has been '.$eventName;
    }
}
