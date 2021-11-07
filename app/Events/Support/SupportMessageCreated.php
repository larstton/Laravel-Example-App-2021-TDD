<?php

namespace App\Events\Support;

use App\Models\SupportRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageCreated
{
    use Dispatchable, SerializesModels;

    public $supportRequest;

    public function __construct(SupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }
}
