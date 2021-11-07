<?php

namespace App\Notifications\Team;

use App\Models\Host;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;

class InstallCagentReminderNotification extends AbstractLoggableReminderNotification
{

    private Host $host;

    /**
     * InstallCagentReminderNotification constructor.
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
            'my.email.reminders.install_cagent_reminder',
            [
                'user' => $notifiable,
                'host' => $this->host,
            ]
        )->subject(__('marketing.ActionInstallCagent'));
    }
}
