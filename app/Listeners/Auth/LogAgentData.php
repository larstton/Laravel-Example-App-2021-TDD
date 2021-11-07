<?php

namespace App\Listeners\Auth;

use App\Events\Auth\NewUserRegistered;
use App\Models\UserAgentData;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAgentData implements ShouldQueue
{
    public function handle(NewUserRegistered $event)
    {
        $user = $event->user;
        $agentData = $event->agentData;

        UserAgentData::create([
            'user_id' => $user->id,
            'team_id' => $user->team_id,
            'data'    => $agentData,
        ]);
    }
}
