<?php

namespace App\Events\Rule;

use App\Models\Rule;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RuleDeleted
{
    use Dispatchable, SerializesModels;

    public $rule;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }
}
