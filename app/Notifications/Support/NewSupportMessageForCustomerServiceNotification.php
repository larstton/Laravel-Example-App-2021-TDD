<?php

namespace App\Notifications\Support;

use App\Mail\SupportRequestAdmin;
use App\Models\SupportRequest;
use App\Notifications\BaseNotification;

class NewSupportMessageForCustomerServiceNotification extends BaseNotification
{
    private $supportRequest;

    public function __construct(SupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }

    public function toMail($notifiable)
    {
        return new SupportRequestAdmin($this->supportRequest);
    }
}
