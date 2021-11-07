<?php


namespace App\Events\Recipient;


use App\Models\Recipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecipientAdministrativelyDisabled
{
    use Dispatchable, SerializesModels;

    public Recipient $recipient;

    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }
}
