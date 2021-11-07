<?php

namespace App\Actions\Recipient;

use App\Models\ActivityLog;
use App\Models\Recipient;

class MuteCommentNotificationsForRecipientAction
{
    public function execute(Recipient $recipient): Recipient
    {
        $recipient->team->makeCurrentTenant();
        $recipient->update([
            'comments' => false,
        ]);

        activity()
            ->causedByAnonymous()
            ->on($recipient)
            ->tap(function (ActivityLog $activity) use ($recipient) {
                $activity->team_id = $recipient->team_id;
            })
            ->log(sprintf("Muted comment notifications for %s %s", $recipient->media_type, $recipient->sendto));

        return $recipient;
    }
}
