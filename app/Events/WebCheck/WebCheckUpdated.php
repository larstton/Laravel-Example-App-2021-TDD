<?php

namespace App\Events\WebCheck;

use App\Models\WebCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebCheckUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * @var WebCheck
     */
    public $webCheck;

    public function __construct(WebCheck $webCheck)
    {
        $this->webCheck = $webCheck;
    }
}
