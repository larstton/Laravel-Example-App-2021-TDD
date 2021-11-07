<?php

namespace App\Actions\Auth;

use App\Actions\Recipient\CreateRecipientAction;
use App\Data\Auth\TeamMemberRegisterData;
use App\Data\Recipient\RecipientData;
use App\Enums\TeamStatus;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

class CompleteRegistrationForTeamMemberAction
{
    private CreateRecipientAction $createRecipientAction;

    public function __construct(CreateRecipientAction $createRecipientAction)
    {
        $this->createRecipientAction = $createRecipientAction;
    }

    public function execute(TeamMember $teamMember, TeamMemberRegisterData $registerData): TeamMember
    {
        return DB::transaction(function () use ($teamMember, $registerData): TeamMember {
            $teamMember->team->makeCurrentTenant();

            $teamMember->update([
                'password'         => $registerData->password,
                'team_status'      => TeamStatus::Joined(),
                'terms_accepted'   => $registerData->termsAccepted,
                'privacy_accepted' => $registerData->privacyAccepted,
                'nickname'         => $registerData->nickname,
            ]);

            if (user_settings($teamMember)->get('makeRecipient')->first()) {
                $this->createRecipientAction->execute(
                    $teamMember,
                    RecipientData::fromTeamMemberSignup($teamMember)
                );
                user_settings($teamMember)->set(['makeRecipient' => false]);
            }

            return $teamMember;
        });
    }
}
