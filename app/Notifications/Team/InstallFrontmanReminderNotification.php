<?php

namespace App\Notifications\Team;

use App\Models\Frontman;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class InstallFrontmanReminderNotification extends AbstractLoggableReminderNotification
{
    private $frontman;

    public function __construct(Frontman $frontman)
    {
        $this->frontman = $frontman;
    }

    public function toMail($notifiable)
    {
        TenantManager::disableTenancyChecks();

        return (new MailMessage)->view(
            'my.email.reminders.install_frontman_reminder',
            [
                'user'     => $notifiable,
                'frontman' => $this->frontman
            ]
        )->subject(__('marketing.ActionInstallFrontman'));
    }
}
