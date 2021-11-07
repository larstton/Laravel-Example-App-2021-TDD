<?php

namespace App\Events\Host;

use App\Models\Host;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HostCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var Host
     */
    public $host;

    public function __construct(Host $host)
    {
        $this->host = $host;
    }
}
