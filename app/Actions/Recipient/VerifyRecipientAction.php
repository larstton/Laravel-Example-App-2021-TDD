<?php

namespace App\Actions\Recipient;

use App\Events\Recipient\RecipientVerified;
use App\Models\Recipient;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyRecipientAction
{
    public function execute(Recipient $recipient, $token): Recipient
    {
        throw_unless(
            hash_equals($token, sha1($recipient->verification_token)),
            new AuthorizationException
        );

        $recipient->team->makeCurrentTenant();

        $recipient->update([
            'verified'                  => true,
            'active'                    => true,
            'administratively_disabled' => false,
            'verified_at'               => now(),
        ]);

        event(new RecipientVerified($recipient));

        return $recipient->refresh();
    }
}
