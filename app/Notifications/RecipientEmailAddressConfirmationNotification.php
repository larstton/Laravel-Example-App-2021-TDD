<?php

namespace App\Notifications;

use App\Models\Recipient;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class RecipientEmailAddressConfirmationNotification extends BaseNotification
{
    public function toMail(Recipient $notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify recipient address at CloudRadar Monitoring')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you aren\'t a user of Cloudradar, ignore this message and delete it.');
    }

    protected function verificationUrl(Recipient $recipient)
    {
        return URL::temporarySignedRoute(
            'klick.recipient.email.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'recipient' => $recipient->id,
                'token'     => sha1($recipient->verification_token),
            ]
        );
    }
}
