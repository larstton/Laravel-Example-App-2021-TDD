<?php

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUserRegistered
{
    use Dispatchable, SerializesModels;

    public User $user;
    public array $agentData;

    public function __construct(User $user, array $agentData)
    {
        $this->user = $user;
        $this->agentData = $agentData;
    }
}
