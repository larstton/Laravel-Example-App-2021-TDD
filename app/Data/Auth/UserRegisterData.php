<?php

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace App\Data\Auth;

use App\Data\BaseData;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\TeamMemberRegisterRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserRegisterData extends BaseData
{
    public string $email;
    public string $password;
    public ?bool $termsAccepted;
    public ?bool $privacyAccepted;
    public ?string $partner;
    public $partnerExtraData;
    public ?string $lang;
    public ?array $registrationTrack = null;
    public ?Carbon $trialEnd;

    public static function fromUserRegisterRequest(RegisterRequest $request): self
    {
        return new self([
            'email'             => Str::lower($request->email),
            'password'          => $request->password,
            'termsAccepted'     => (bool) $request->termsAccepted,
            'privacyAccepted'   => (bool) $request->privacyAccepted,
            'trialEnd'          => $request->getTrialEnd(),
            'lang'              => $request->lang,
            'partner'           => $request->partner,
            'partnerExtraData'  => $request->partnerExtraData,
            'registrationTrack' => $request->registrationTrack ?
                json_decode($request->registrationTrack, true)
                : null,
        ]);
    }

    public static function fromTeamMemberRegisterRequest(TeamMemberRegisterRequest $request): self
    {
        return new self([
            'email'            => Str::lower($request->email),
            'password'         => $request->password,
            'termsAccepted'    => (bool) $request->termsAccepted,
            'privacyAccepted'  => (bool) $request->privacyAccepted,
            'trialEnd'         => null,
            'lang'             => null,
            'partner'          => null,
            'partnerExtraData' => null,
        ]);
    }
}
