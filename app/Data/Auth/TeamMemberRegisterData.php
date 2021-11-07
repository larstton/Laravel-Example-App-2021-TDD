<?php

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace App\Data\Auth;

use App\Data\BaseData;
use App\Http\Requests\Auth\TeamMemberRegisterRequest;

class TeamMemberRegisterData extends BaseData
{
    public ?string $nickname;
    public string $password;
    public bool $termsAccepted;
    public bool $privacyAccepted;

    public static function fromTeamMemberRegisterRequest(TeamMemberRegisterRequest $request): self
    {
        return new self([
            'nickname'        => $request->nickname,
            'password'        => $request->password,
            'termsAccepted'   => (bool) $request->termsAccepted,
            'privacyAccepted' => (bool) $request->privacyAccepted,
        ]);
    }
}
