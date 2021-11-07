<?php

namespace App\Actions\Team;

use App\Enums\RecipientMediaType;
use App\Models\Host;
use App\Models\Recipient;
use App\Models\Tag;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

class DeleteTeamMemberAction
{
    public function execute(TeamMember $teamMember, $removeRecipient = false)
    {
        DB::transaction(function () use ($teamMember, $removeRecipient) {
            if ($removeRecipient) {
                Recipient::whereMediaType(RecipientMediaType::Email())
                    ->whereSendto($teamMember->email)
                    ->delete();
            }

            $teamMember->delete();

            if ($teamMember->host_tag && $tag = Tag::findFromString($teamMember->host_tag, Host::getTagType())) {
                $tag->removeTeamMemberFilterFromMeta($teamMember)->save();
            }
        });
    }
}
