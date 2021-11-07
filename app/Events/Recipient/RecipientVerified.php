<?php

namespace App\Events\Recipient;

use App\Models\Recipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecipientVerified
{
    use Dispatchable, SerializesModels;

    public $recipient;

    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }
}
