<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSettingsUpdated
{
    use Dispatchable, SerializesModels;

    public User $user;
    public array $settings;

    public function __construct(User $user, array $settings)
    {
        $this->user = $user;
        $this->settings = $settings;
    }
}
