<?php

namespace App\Actions\Recipient;

use App\Events\Recipient\RecipientUnsubscribedFromDailySummary;
use App\Models\ActivityLog;
use App\Models\Recipient;

class UnsubscribeRecipientFromDailySummaryAction
{
    public function execute(Recipient $recipient): Recipient
    {
        $recipient->team->makeCurrentTenant();

        $recipient->update([
            'daily_summary' => false,
        ]);

        activity()
            ->causedByAnonymous()
            ->on($recipient)
            ->tap(function (ActivityLog $activity) use ($recipient) {
                $activity->team_id = $recipient->team_id;
            })
            ->log(sprintf("Summary for recipient \"%s\" disabled", $recipient->sendto));

        RecipientUnsubscribedFromDailySummary::dispatch($recipient);

        return $recipient->refresh();
    }
}
