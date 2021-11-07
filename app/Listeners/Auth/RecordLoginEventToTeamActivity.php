<?php

namespace App\Listeners\Auth;

use App\Events\Auth\UserLoggedIn;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordLoginEventToTeamActivity implements ShouldQueue
{
    public function handle(UserLoggedIn $event)
    {
        TenantManager::setCurrentTenant($event->user->team);
        activity()
            ->causedBy($event->user)
            ->tap(fn ($activity) => $activity->team_id = $event->user->team_id)
            ->log("{$event->user->email} logged in.");
    }
}
