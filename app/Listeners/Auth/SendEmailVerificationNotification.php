<?php

namespace App\Listeners\Auth;

use App\Events\Auth\NewUserRegistered;

class SendEmailVerificationNotification
{
    public function handle(NewUserRegistered $event)
    {
        $event->user->sendEmailVerificationNotification();
    }
}
