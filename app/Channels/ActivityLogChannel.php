<?php

namespace App\Channels;

use App\Models\ActivityLog;
use App\Models\Team;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ActivityLogChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $type = class_basename(get_class($notification));
        $text = sprintf('received %s', Str::of(Str::snake($type))->replace('_', ' '));
        /** @var Team $team */
        $team = tap($notifiable->team)->makeCurrentTenant();
        activity()
            ->causedBy($notifiable)
            ->tap(function (ActivityLog $activity) use ($team, $notifiable, $type) {
                $activity->team_id = $team->id;
                $activity->properties = [
                    'reminder' => 'reminder',
                    'type'     => $type,
                ];
                $activity->log_name = 'system';
            })
            ->log($text);
    }
}
