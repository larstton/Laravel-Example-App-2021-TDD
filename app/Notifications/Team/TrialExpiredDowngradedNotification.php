<?php

namespace App\Notifications\Team;

use App\Models\Team;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TrialExpiredDowngradedNotification extends BaseNotification
{
    public $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->subject('Your CloudRadar trial has expired.')
            ->markdown('mail.team.trial-expired', [
                'loginUrl' => route('web.login', [
                    'email' => $notifiable->email,
                ]),
            ]);
    }
}
