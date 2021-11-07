<?php

namespace App\Http\Resources;

use App\Enums\RecipientMediaType;
use App\Http\Transformers\DateTransformer;
use App\Http\Transformers\IntegromatDataTransformer;
use App\Models\Recipient;

/**
 * @mixin Recipient
 */
class RecipientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'sendto'                   => $this->sendto,
            'mediatype'                => $this->media_type->value,
            'option1'                  => $this->option1,
            'description'              => $this->description ?? null,
            'warnings'                 => $this->warnings,
            'alerts'                   => $this->alerts,
            'reminders'                => $this->reminders,
            'recoveries'               => $this->recoveries,
            'active'                   => $this->active,
            'verified'                 => $this->verified,
            'dailyReports'             => $this->daily_reports,
            'weeklyReports'            => $this->weekly_reports,
            'monthlyReports'           => $this->monthly_reports,
            'dailySummary'             => $this->daily_summary,
            'comments'                 => $this->comments,
            'reminderDelay'            => $this->reminder_delay,
            'maximumReminders'         => $this->maximum_reminders,
            'eventUuids'               => $this->event_uuids,
            'permanentFailuresLast24h' => $this->permanent_failures_last_24_h,
            'rules'                    => $this->rules,
            'extraData'                => $this->transformExtraData(),
            'dates'                    => [
                'verifiedAt' => DateTransformer::transform($this->verified_at),
                'updatedAt'  => DateTransformer::transform($this->updated_at),
                'createdAt'  => DateTransformer::transform($this->created_at),
            ],
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'maxRecipients'     => current_team()->max_recipients,
            ],
        ];
    }

    private function transformExtraData()
    {
        if ($this->media_type->is(RecipientMediaType::Integromat())) {
            return ['integromat' => IntegromatDataTransformer::forFrontend($this->extra_data['integromat'])];
        }

        return $this->extra_data;
    }
}
