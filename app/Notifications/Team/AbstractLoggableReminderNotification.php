<?php

namespace App\Notifications\Team;

use App\Channels\ActivityLogChannel;
use App\Notifications\BaseNotification;

class AbstractLoggableReminderNotification extends BaseNotification
{
    public function via($notifiable)
    {
        return ['mail', ActivityLogChannel::class];
    }
}
