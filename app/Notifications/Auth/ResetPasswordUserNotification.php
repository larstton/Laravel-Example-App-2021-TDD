<?php

namespace App\Notifications\Auth;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordUserNotification extends BaseNotification
{
    private $token;
    private $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function toMail($notifiable)
    {
        $url = route('web.password.reset', [
            'email' => $this->email,
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get(
                'This password reset link will expire in :count minutes.',
                ['count' => config('auth.passwords.users.expire')]
            ))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }
}
