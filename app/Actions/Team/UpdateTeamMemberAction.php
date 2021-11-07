<?php

namespace App\Actions\Team;

use App\Data\Team\UpdateTeamMemberData;
use App\Models\Host;
use App\Models\Tag;
use App\Models\TeamMember;

class UpdateTeamMemberAction
{
    public function execute(TeamMember $teamMember, UpdateTeamMemberData $data): TeamMember
    {
        $teamMember->update([
            'role'        => $data->get('role', $teamMember->role) ?? $teamMember->role,
            'host_tag'    => $data->get('hostTag', $oldTag = $teamMember->host_tag),
            'sub_unit_id' => optional($data->get('subUnit', $teamMember->subUnit))->id,
        ]);

        $tagType = Host::getTagType();

        if ($oldTag && $tag = Tag::findFromString($oldTag, $tagType)) {
            $tag->removeTeamMemberFilterFromMeta($teamMember)->save();
        }
        if ($teamMember->host_tag && $tag = Tag::findFromString($teamMember->host_tag, $tagType)) {
            $tag->addTeamMemberFilterToMeta($teamMember)->save();
        }

        return $teamMember;
    }
}
