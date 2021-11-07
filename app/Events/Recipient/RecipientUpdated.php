<?php

namespace App\Events\Recipient;

use App\Models\Recipient;
use Illuminate\Foundation\Events\Dispatchable;

class RecipientUpdated
{
    use Dispatchable;

    public $recipient;

    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }
}
