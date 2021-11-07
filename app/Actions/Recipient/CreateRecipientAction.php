<?php

namespace App\Actions\Recipient;

use App\Data\Recipient\RecipientData;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Support\Str;

class CreateRecipientAction
{
    public function execute(User $user, RecipientData $recipientData): ?Recipient
    {
        if (Recipient::whereSendto($recipientData->sendto)
            ->whereMediaType($recipientData->mediatype)->exists()) {
            return null;
        }

        return Recipient::create([
            'media_type'                   => $recipientData->mediatype,
            'sendto'                       => $recipientData->sendto,
            'option1'                      => $recipientData->option1,
            'description'                  => $recipientData->description,
            'alerts'                       => $recipientData->alerts,
            'warnings'                     => $recipientData->warnings,
            'reminders'                    => $recipientData->reminders,
            'comments'                     => $recipientData->comments,
            'event_uuids'                  => $recipientData->eventUuids,
            'recoveries'                   => $recipientData->recoveries,
            'active'                       => $recipientData->active,
            'daily_summary'                => $recipientData->dailySummary,
            'daily_reports'                => $recipientData->dailyReports,
            'weekly_reports'               => $recipientData->weeklyReports,
            'monthly_reports'              => $recipientData->monthlyReports,
            'reminder_delay'               => $recipientData->reminderDelay,
            'maximum_reminders'            => $recipientData->maximumReminders,
            'rules'                        => $recipientData->rules,
            'extra_data'                   => $recipientData->extraData,
            'team_id'                      => $user->team_id,
            'user_id'                      => $user->id,
            'verification_token'           => Str::random(8),
            'verified'                     => $recipientData->verified,
            'permanent_failures_last_24_h' => 0,
        ]);
    }
}
