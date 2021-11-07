<?php

namespace App\Notifications\Team;

use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class TeamMemberInvitationNotification extends BaseNotification
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toMail(TeamMember $notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        $mail = new MailMessage;

        if ($this->shouldBCCTestInboxForAutomation($notifiable)) {
            $mail->bcc('mailapi.support@automation.com');
        }

        return $mail
            ->subject('Invitation to a monitoring team on CloudRadar.io.')
            ->line("The user {$this->user->email} invites you to join a monitoring team.")
            ->action('Join the team', $verificationUrl)
            ->line("If you don't know the person who's invited you, you can safely ignore this message.");
    }

    protected function verificationUrl(TeamMember $teamMember)
    {
        return URL::temporarySignedRoute(
            'klick.team-member.email.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'teamMember' => $teamMember->id,
                'token'      => sha1($teamMember->getEmailForVerification()),
            ]
        );
    }

    private function shouldBCCTestInboxForAutomation(TeamMember $notifiable): bool
    {
        if (! app()->environment(['local', 'staging'])) {
            return false;
        }

        if (! is_cloud_radar_support_email($notifiable->email)) {
            return false;
        }

        if (! Str::contains($this->user->email, 'mailapi')) {
            return false;
        }

        return true;
    }
}
