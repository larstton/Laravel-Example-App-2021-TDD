<?php

namespace App\Data\Team;

use App\Data\BaseData;
use App\Enums\TeamMemberRole;
use App\Http\Requests\Team\UpdateTeamMemberRequest;
use App\Models\SubUnit;

class UpdateTeamMemberData extends BaseData
{
    public ?TeamMemberRole $role;
    public ?string $hostTag;
    public ?SubUnit $subUnit;

    public static function fromRequest(UpdateTeamMemberRequest $request): self
    {
        return (new self([
            'role'    => TeamMemberRole::coerce($request->role),
            'hostTag' => $request->hostTag,
            'subUnit' => SubUnit::find($request->subUnit),
        ]))->setHasData([
            'role'    => $request->has('role'),
            'hostTag' => $request->has('hostTag'),
            'subUnit' => $request->has('subUnit'),
        ]);
    }
}
