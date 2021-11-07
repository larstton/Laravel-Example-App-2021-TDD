<?php

namespace Tests\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Tests\Concerns\MailMessageTester;

trait MailHelpers
{
    public function buildMailMessageTester(MailMessage $mailMessage)
    {
        return new MailMessageTester($mailMessage, $this);
    }
}
