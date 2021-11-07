<?php

namespace App\Events\CustomCheck;

use App\Models\CustomCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomCheckCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var CustomCheck
     */
    public $customCheck;

    public function __construct(CustomCheck $customCheck)
    {
        $this->customCheck = $customCheck;
    }
}
