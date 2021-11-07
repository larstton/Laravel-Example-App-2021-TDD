<?php

namespace App\Listeners\Support;

use App\Events\Support\SupportMessageCreated;
use App\Mail\SupportRequestAdmin;
use App\Notifications\Support\NewSupportMessageCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendSupportRequestConfirmation implements ShouldQueue
{
    public function handle(SupportMessageCreated $event)
    {
        // Notify user
        Notification::route('mail', $event->supportRequest->email)
            ->notify(new NewSupportMessageCreatedNotification($event->supportRequest));

        // Send email to customer Service
        Mail::to(config('cloudradar.support.support_email'))
            ->send(new SupportRequestAdmin($event->supportRequest));
    }
}
