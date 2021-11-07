<?php

namespace App\Events\Host;

use App\Models\Host;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HostDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * @var Host
     */
    public $host;

    /**
     * Create a new event instance.
     *
     * @param  Host  $host
     */
    public function __construct(Host $host)
    {
        $this->host = $host;
    }
}
