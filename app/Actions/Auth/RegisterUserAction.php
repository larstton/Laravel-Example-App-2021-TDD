<?php

namespace App\Actions\Auth;

use App\Actions\Recipient\CreateRecipientAction;
use App\Actions\Team\CreateTeamAction;
use App\Data\Auth\UserRegisterData;
use App\Data\Recipient\RecipientData;
use App\Data\Team\CreateTeamData;
use App\Enums\TeamStatus;
use App\Events\Auth\NewTeamCreated;
use App\Events\Auth\NewUserRegistered;
use App\Exceptions\TeamException;
use App\Models\User;
use App\Support\AgentData\AgentData;
use App\Support\Rule\RuleFactory;
use App\Support\Validation\BanList;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    private CreateTeamAction $createTeamAction;
    private CreateRecipientAction $createRecipientAction;
    private AgentData $agentData;

    public function __construct(
        CreateTeamAction $createTeamAction,
        CreateRecipientAction $createRecipientAction,
        AgentData $agentData
    ) {
        $this->createTeamAction = $createTeamAction;
        $this->createRecipientAction = $createRecipientAction;
        $this->agentData = $agentData;
    }

    public function execute(UserRegisterData $registerData): User
    {
        $this->guard($registerData);

        return DB::transaction(function () use ($registerData): User {
            $team = $this->createTeamAction->execute(
                CreateTeamData::fromUserRegisterData($registerData)
            );

            $team->makeCurrentTenant();

            $user = User::create([
                'team_id'          => $team->id,
                'email'            => $registerData->email,
                'password'         => $registerData->password,
                'terms_accepted'   => $registerData->termsAccepted,
                'privacy_accepted' => $registerData->privacyAccepted,
                'product_news'     => true,
                'team_status'      => TeamStatus::Joined(),
                'lang'             => $registerData->lang ?? 'en',
            ]);

            // This will ensure that the activity log is linked correctly to this new user.
            auth()->setUser($user);

            RuleFactory::makeGeneralSuccessAlertRule($user)->save();

            $this->createRecipientAction->execute(
                $user,
                RecipientData::fromNewTeamUserSignup($user)
            );

            NewUserRegistered::dispatch($user, $this->agentData->all());
            NewTeamCreated::dispatch($user, $team);

            return $user;
        });
    }

    private function guard(UserRegisterData $registerData)
    {
        $this->banAssertions($registerData);
    }

    protected function banAssertions($registerData): void
    {
        throw_if(BanList::isEmailBanned($registerData->email),
            TeamException::bannedEmailProvided($registerData->email)
        );
    }
}
