<?php

namespace App\Events\Team;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberInvited
{
    use Dispatchable, SerializesModels;

    public $teamMember;
    public $user;

    public function __construct(TeamMember $teamMember, User $user)
    {
        $this->teamMember = $teamMember;
        $this->user = $user;
    }
}
