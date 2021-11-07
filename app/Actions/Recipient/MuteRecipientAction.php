<?php

namespace App\Actions\Recipient;

use App\Models\ActivityLog;
use App\Models\Recipient;

class MuteRecipientAction
{
    public function execute(Recipient $recipient): Recipient
    {
        $recipient->team->makeCurrentTenant();
        $recipient->update([
            'alerts'    => false,
            'warnings'  => false,
            'reminders' => false,
        ]);

        activity()
            ->causedByAnonymous()
            ->on($recipient)
            ->tap(function (ActivityLog $activity) use ($recipient) {
                $activity->team_id = $recipient->team_id;
            })
            ->log(sprintf("Disabled alerting for %s %s", $recipient->media_type, $recipient->sendto));

        return $recipient->refresh();
    }
}
