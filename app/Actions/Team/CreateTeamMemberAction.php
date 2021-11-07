<?php

namespace App\Actions\Team;

use App\Data\Team\CreateTeamMemberData;
use App\Enums\TeamStatus;
use App\Events\Team\TeamMemberInvited;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTeamMemberAction
{
    public function execute(User $user, CreateTeamMemberData $teamMemberData): TeamMember
    {
        $teamMember = DB::transaction(function () use ($teamMemberData, $user) {
            /** @var TeamMember $teamMember */
            $teamMember = TeamMember::create([
                'email'            => $teamMemberData->email,
                'terms_accepted'   => false,
                'privacy_accepted' => false,
                'product_news'     => false,
                'role'             => $teamMemberData->role,
                'team_status'      => TeamStatus::Invited(),
                'lang'             => $user->lang ?? 'en',
                'password'         => Str::random(),
            ]);

            if ($teamMemberData->createRecipient && ! is_cloud_radar_support_email($teamMember->email)) {
                user_settings($teamMember)->set(['makeRecipient' => true]);
            }

            if ($this->shouldExtendTrialForSupport($teamMember)) {
                $teamMember->team->update([
                    'trial_ends_at' => $teamMember->team->trial_ends_at->addDays(5),
                ]);
            }

            return $teamMember;
        });

        TeamMemberInvited::dispatchIf($teamMember->exists(), $teamMember, $user);

        return $teamMember;
    }

    private function shouldExtendTrialForSupport(TeamMember $teamMember): bool
    {
        if (! is_cloud_radar_support_email($teamMember->email)) {
            return false;
        }

        if (is_null($teamMember->team->trial_ends_at)) {
            return false;
        }

        if ($teamMember->team->trial_ends_at->isAfter(now()->addDays(5))) {
            return false;
        }

        return true;
    }
}
