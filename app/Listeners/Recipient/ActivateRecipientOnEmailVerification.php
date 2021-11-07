<?php

namespace App\Listeners\Recipient;

use App\Actions\Recipient\ActivateTeamAdminRecipientAction;
use App\Models\User;
use Illuminate\Auth\Events\Verified as UserVerifiedEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivateRecipientOnEmailVerification implements ShouldQueue
{
    private $activateTeamAdminRecipientAction;

    public function __construct(ActivateTeamAdminRecipientAction $activateTeamAdminRecipientAction)
    {
        $this->activateTeamAdminRecipientAction = $activateTeamAdminRecipientAction;
    }

    public function handle(UserVerifiedEmail $event)
    {
        /** @var User $user */
        $user = $event->user;
        $user->team->makeCurrentTenant();
        $this->activateTeamAdminRecipientAction->execute($user);
    }
}
