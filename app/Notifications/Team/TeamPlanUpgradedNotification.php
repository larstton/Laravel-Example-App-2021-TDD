<?php

namespace App\Notifications\Team;

use App\Models\Team;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamPlanUpgradedNotification extends BaseNotification
{
    public $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->subject('Your account has been upgraded')
            ->line("Your account has been upgraded to the new plan {$this->team->plan}.");
    }
}
