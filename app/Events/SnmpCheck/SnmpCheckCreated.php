<?php

namespace App\Events\SnmpCheck;

use App\Models\SnmpCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SnmpCheckCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var SnmpCheck
     */
    public $snmpCheck;

    public function __construct(SnmpCheck $snmpCheck)
    {
        $this->snmpCheck = $snmpCheck;
    }
}
