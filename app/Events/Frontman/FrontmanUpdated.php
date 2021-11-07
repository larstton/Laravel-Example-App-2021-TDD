<?php

namespace App\Events\Frontman;

use App\Models\Frontman;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FrontmanUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * @var Frontman
     */
    public $frontman;

    public function __construct(Frontman $frontman)
    {
        $this->frontman = $frontman;
    }
}
