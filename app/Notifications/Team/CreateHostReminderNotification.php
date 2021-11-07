<?php

namespace App\Notifications\Team;

use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Notifications\Messages\MailMessage;

class CreateHostReminderNotification extends AbstractLoggableReminderNotification
{
    public function toMail($notifiable)
    {
        TenantManager::disableTenancyChecks();

        return (new MailMessage)->view(
            'my.email.reminders.create_host_reminder',
            [
                'user' => $notifiable,
            ]
        )->subject(__('marketing.ActionCreateHost'));
    }
}
