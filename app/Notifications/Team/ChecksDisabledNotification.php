<?php


namespace App\Notifications\Team;


use App\Data\Utility\DisabledChecksData;
use Illuminate\Notifications\Messages\MailMessage;

class ChecksDisabledNotification extends AbstractLoggableReminderNotification
{
    protected DisabledChecksData $checkData;

    public function __construct(DisabledChecksData $checkData)
    {
        $this->checkData = $checkData;
    }

    public function toMail($notifiable)
    {
        $this->locale('en');

        return (new MailMessage)->view(
            'my.email.checks_disabled_notification',
            [
                'user' => $notifiable,
                'data' => $this->checkData->getChecks()
            ]
        )->subject(__('marketing.ActionChecksDisabled'));
    }
}
