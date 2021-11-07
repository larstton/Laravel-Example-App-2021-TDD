<?php

namespace App\Notifications;

use App\Models\EventComment;
use App\Models\Recipient;
use Illuminate\Notifications\Messages\MailMessage;

class NewCommentAddedRecipientNotification extends BaseNotification
{
    private EventComment $eventComment;
    private Recipient $recipient;

    public function __construct(EventComment $eventComment, Recipient $recipient)
    {
        $this->eventComment = $eventComment;
        $this->recipient = $recipient;
    }

    public function toMail($notifiable)
    {
        $subject = 'New comment has been added to the event';
        if ($this->recipient->event_uuids) {
            $subject .= ": {$this->eventComment->event_id}";
        }

        return (new MailMessage)
            ->greeting('Hi')
            ->subject($subject)
            ->line("A team member \"{$this->eventComment->user->email}\" added the following comment to:")
            ->line('Event: ' .$this->eventComment->event->meta->name)
            ->line('Severity: ' .ucfirst($this->eventComment->event->action))
            ->line('Host: ' .$this->eventComment->event->host->name .' (' .$this->eventComment->event->host->connect .')')
            ->line('Text: ' .$this->eventComment->text)
            ->action('Unsubscribe from future comment notifications', $this->unsubscribeUrl());
    }

    protected function unsubscribeUrl()
    {
        return url()->signedRoute('klick.mute-comments.edit', [
            'recipient' => $this->recipient->id,
        ]);
    }
}
