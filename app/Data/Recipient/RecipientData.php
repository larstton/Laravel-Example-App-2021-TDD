<?php

namespace App\Data\Recipient;

use App\Data\BaseData;
use App\Enums\RecipientMediaType;
use App\Http\Requests\Recipient\RecipientRequest;
use App\Http\Transformers\IntegromatDataTransformer;
use App\Models\TeamMember;
use App\Models\User;

class RecipientData extends BaseData
{
    public RecipientMediaType $mediatype;
    public string $sendto;
    public ?string $option1 = null;
    public ?string $description = null;
    public bool $alerts = true;
    public bool $warnings = true;
    public bool $reminders = true;
    public bool $recoveries = false;
    public bool $comments = false;
    public bool $eventUuids = false;
    public bool $dailySummary = true;
    public bool $weeklyReports = true;
    public bool $dailyReports = true;
    public bool $monthlyReports = true;
    public bool $active = true;
    public int $reminderDelay = 14400;
    public int $maximumReminders = 6;
    public ?array $rules = null;
    public ?array $extraData = null;
    public bool $verified = false;

    public static function fromRequest(RecipientRequest $request): self
    {
        return new self([
            'mediatype'        => RecipientMediaType::coerce($request->mediatype),
            'sendto'           => $request->sendto,
            'option1'          => $request->option1,
            'description'      => $request->description,
            'comments'         => (bool) $request->comments,
            'warnings'         => (bool) $request->warnings,
            'eventUuids'       => (bool) $request->eventUuids,
            'alerts'           => (bool) $request->alerts,
            'reminders'        => (bool) $request->reminders,
            'recoveries'       => (bool) $request->recoveries,
            'active'           => (bool) $request->active,
            'dailySummary'     => (bool) $request->dailySummary,
            'dailyReports'     => (bool) $request->dailyReports,
            'weeklyReports'    => (bool) $request->weeklyReports,
            'monthlyReports'   => (bool) $request->monthlyReports,
            'reminderDelay'    => (int) $request->reminderDelay,
            'maximumReminders' => (int) $request->maximumReminders,
            'rules'            => $request->rules === 0 ? null : $request->rules,
            'extraData'        => self::transformExtraData($request),
        ]);
    }

    public static function fromNewTeamUserSignup(User $user): self
    {
        return new self([
            'mediatype'    => RecipientMediaType::Email(),
            'sendto'       => $user->email,
            'dailyReports' => false,
            'verified'     => false,
            'comments'     => false,
            'active'       => false,
            'recoveries'   => true,
        ]);
    }

    public static function fromTeamMemberSignup(TeamMember $teamMember): self
    {
        return new self([
            'mediatype' => RecipientMediaType::Email(),
            'sendto'    => $teamMember->email,
            'comments'  => false,
            'verified'  => true,
            'active'    => true,
        ]);
    }

    private static function transformExtraData(RecipientRequest $request)
    {
        $mediatype = RecipientMediaType::coerce($request->mediatype);

        if ($mediatype->is(RecipientMediaType::Integromat())) {
            return ['integromat' => IntegromatDataTransformer::fromRequest($request->extraData['integromat'])];
        }

        return collect($request->extraData)->toArray();
    }
}
