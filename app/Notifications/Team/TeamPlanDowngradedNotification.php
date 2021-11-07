<?php

namespace App\Notifications\Team;

use App\Models\Team;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class TeamPlanDowngradedNotification extends BaseNotification
{
    public $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->subject('Your account has been downgraded')
            ->line(new HtmlString('Your account has been downgraded and is now frozen. <strong> All your monitoring is now stopped. </strong>'))
            ->line('Your data and configuration will be stored for 60 days. You can re-activate your account at any time by upgrading to a paid plan again.')
            ->line('If you like, you can log-in to delete the account and all your data irretrievably.')
            ->line("If you don't do so, your account and all data will be deleted after 60 days automatically.");
    }
}
