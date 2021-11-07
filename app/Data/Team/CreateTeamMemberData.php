<?php

namespace App\Data\Team;

use App\Data\BaseData;
use App\Enums\TeamMemberRole;
use App\Http\Requests\Team\CreateTeamMemberRequest;

class CreateTeamMemberData extends BaseData
{
    public string $email;
    public TeamMemberRole $role;
    public bool $createRecipient;

    public static function fromRequest(CreateTeamMemberRequest $request): self
    {
        return new self([
            'email'           => $request->email,
            'role'            => TeamMemberRole::coerce($request->role),
            'createRecipient' => (bool) $request->input('createRecipient', false),
        ]);
    }
}
