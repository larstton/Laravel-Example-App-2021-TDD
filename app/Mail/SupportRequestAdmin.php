<?php

namespace App\Mail;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SupportRequestAdmin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var SupportRequest
     */
    public $supportRequest;

    public function __construct(SupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }

    public function build()
    {
        $mail = $this
            ->subject($this->supportRequest->subject)
            ->markdown('mail.support-request.admin', [
                'subject' => strip_tags($this->supportRequest->subject),
                'body'    => strip_tags($this->supportRequest->body),
                'team'    => $this->supportRequest->team,
            ])
            ->replyTo($this->supportRequest->email);

        if ($this->supportRequest->attachment) {
            foreach ($this->supportRequest->attachment as $attachment) {
                $mail->attach(
                    Storage::disk('support_attachments')->path(
                        "{$this->supportRequest->id}/{$attachment}"
                    )
                );
            }
        }

        return $mail;
    }
}
