<?php

namespace App\Events\ServiceCheck;

use App\Models\ServiceCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceCheckDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * @var ServiceCheck
     */
    public $serviceCheck;

    public function __construct(ServiceCheck $serviceCheck)
    {
        $this->serviceCheck = $serviceCheck;
    }
}
