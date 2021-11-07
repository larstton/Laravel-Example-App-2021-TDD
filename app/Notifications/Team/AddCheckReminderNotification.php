<?php

namespace App\Notifications\Team;

use App\Models\ActivityLog;
use App\Models\Host;
use App\Models\User;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

class AddCheckReminderNotification extends AbstractLoggableReminderNotification
{
    private Host $host;

    /**
     * AddCheckReminderNotification constructor.
     * @param  Host  $host
     */
    public function __construct(Host $host)
    {
        $this->host = $host;
    }


    public function toMail($notifiable)
    {
        TenantManager::disableTenancyChecks();

        return (new MailMessage)->view(
            'my.email.reminders.add_check_reminder',
            [
                'user' => $notifiable,
                'host' => $this->host,
            ]
        )->subject(__('marketing.ActionAddCheck'));
    }
}
