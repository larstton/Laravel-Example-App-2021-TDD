<?php

namespace App\Notifications\Auth;

use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class NewUserEmailVerificationNotification extends BaseNotification
{
    protected $campaign;
    protected $content;

    public function __construct($campaign = 'verification', $content = null)
    {
        $this->campaign = $this->content = $campaign;
        if (! is_null($content)) {
            $this->content = $content;
        }
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        App::setLocale($notifiable->lang);

        return (new MailMessage)
            ->greeting(__('registration.WelcomeTo').' CloudRadar Monitoring')
            ->subject(__('registration.Complete your registration at CloudRadar Monitoring'))
            ->line(__('registration.YouHaveRegisteredForAFree15DayTrial'))
            //we don't use json-based translation file, so there is no way to translate 'Regards' key,
            //so copy default salutation here and translate with `registration.Regards` key
            ->salutation(new HtmlString(__('registration.Regards').",<br>".config('app.name')))
            ->action(__('registration.VerifyEmail'), $verificationUrl)
            ->line(__('registration.IgnoreIfNotRegistered'));
    }

    protected function verificationUrl(User $user)
    {
        return URL::temporarySignedRoute(
            'klick.register.email.verify',
            now()->addMinutes(config('auth.verification.expire')),
            [
                'user'         => $user->id,
                'id'           => $user->getKey(),
                'hash'         => sha1($user->getEmailForVerification()),
                'utm_source'   => 'email',
                'utm_medium'   => 'email',
                'utm_campaign' => $this->campaign,
                'utm_content'  => $this->content,
            ]
        );
    }
}
