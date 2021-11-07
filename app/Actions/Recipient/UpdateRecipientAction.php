<?php

namespace App\Actions\Recipient;

use App\Data\Recipient\RecipientData;
use App\Enums\RecipientMediaType;
use App\Exceptions\RecipientException;
use App\Models\Recipient;

class UpdateRecipientAction
{
    public function execute(Recipient $recipient, RecipientData $recipientData)
    {
        throw_if(
            $recipient->media_type->is(RecipientMediaType::Email())
            && $recipient->sendto !== $recipientData->sendto,
            RecipientException::changingEmailIsForbidden()
        );

        return tap($recipient)->update([
            'sendto'            => $recipientData->sendto,
            'option1'           => $recipientData->option1 ?? null,
            'description'       => $recipientData->description ?? null,
            'alerts'            => $recipientData->alerts,
            'warnings'          => $recipientData->warnings,
            'reminders'         => $recipientData->reminders,
            'recoveries'        => $recipientData->recoveries,
            'active'            => $recipientData->active,
            'comments'          => $recipientData->comments,
            'event_uuids'       => $recipientData->eventUuids,
            'daily_summary'     => $recipientData->dailySummary,
            'daily_reports'     => $recipientData->dailyReports,
            'weekly_reports'    => $recipientData->weeklyReports,
            'monthly_reports'   => $recipientData->monthlyReports,
            'reminder_delay'    => $recipientData->reminderDelay,
            'maximum_reminders' => $recipientData->maximumReminders,
            'rules'             => $recipientData->rules,
            'extra_data'        => $recipientData->extraData,
        ]);
    }
}
