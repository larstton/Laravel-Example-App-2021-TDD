<?php

namespace App\Notifications\Support;

use App\Models\SupportRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class NewSupportMessageCreatedNotification extends BaseNotification
{
    private $supportRequest;

    public function __construct(SupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Support Request has been created')
            ->greeting('Dear customer')
            ->line('you have created a support request and we are working on it.')
            ->line('Our support team answers requests from Monday to Friday 6.00 - 18.00 UTC.')
            ->line('You should receive an answer shortly.')
            ->line('')
            ->line('You have submitted the following message:')
            ->line(new HtmlString('<strong>Your subject</strong>'))
            ->line(new HtmlString(strip_tags($this->supportRequest->subject)))
            ->line(new HtmlString('<strong>Your message</strong>'))
            ->line(new HtmlString(strip_tags($this->supportRequest->body)))
            ->when(! is_null($this->supportRequest->attachment), function ($mail) {
                return $mail
                    ->line(new HtmlString('<strong>Received attachments</strong>'))
                    ->line(implode(', ', $this->supportRequest->attachment));
            });
    }
}
