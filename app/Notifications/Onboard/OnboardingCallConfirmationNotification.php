<?php

namespace App\Notifications\Onboard;

use App\Models\Team;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class OnboardingCallConfirmationNotification extends BaseNotification
{
    use Queueable;

    private User $user;
    private Team $team;

    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your personal onboarding call to CloudRadar is underway')
            ->line('I would like to make an appointment for the onboarding call (max. 20 min. (longer if you desire), online, German or English).')
            ->line('Please contact me at:')
            ->line('Name: '.$this->user->name)
            ->line('Company: '.$this->team->company_name)
            ->line('Phone: '.$this->team->company_phone);
    }
}
