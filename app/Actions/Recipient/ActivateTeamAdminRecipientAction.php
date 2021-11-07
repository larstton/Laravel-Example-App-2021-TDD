<?php

namespace App\Actions\Recipient;

use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Models\User;

class ActivateTeamAdminRecipientAction
{
    public function execute(User $user): void
    {
        optional(Recipient::query()
            ->whereSendto($user->email)
            ->whereMediaType(RecipientMediaType::Email())
            ->first())
            ->update([
                'verified'                  => true,
                'active'                    => true,
                'administratively_disabled' => false,
                'verified_at'               => now(),
            ]);
    }
}
