<?php

namespace App\Actions\Recipient;

use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientAdministrativelyDisabled;
use App\Models\Recipient;

class AdministrativelyDisablePhoneCallRecipientsBySendToAction
{
    public function execute(string $sendto)
    {
        Recipient::query()
            ->whereSendto($sendto)
            ->whereMediaType(RecipientMediaType::Phonecall())
            ->active()
            ->each(fn (Recipient $recipient) => $this->disableRecipient($recipient));
    }

    private function disableRecipient(Recipient $recipient)
    {
        $recipient->team->makeCurrentTenant();

        $recipient->update([
            'administratively_disabled' => true,
            'active'                    => false,
        ]);

        activity()
            ->causedByAnonymous()
            ->on($recipient)
            ->tap(function ($activity) use ($recipient) {
                $activity->team_id = $recipient->team_id;
            })
            ->log(sprintf("Recipient \"%s\" administratively disabled", $recipient->sendto));

        RecipientAdministrativelyDisabled::dispatch($recipient);
    }
}
