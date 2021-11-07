<?php

namespace App\Actions\Onboard;

use App\Data\Onboard\OnboardStep2Data;
use App\Models\Team;
use App\Models\User;
use App\Notifications\Onboard\OnboardingCallConfirmationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SaveOnboardingPayloadStep2Action
{
    public function execute(OnboardStep2Data $data, User $user, Team $team)
    {
        DB::transaction(function () use ($data, $team, $user) {
            $user->update([
                'name' => $data->name,
            ]);

            $team->fill([
                'company_name'  => $data->companyName,
                'company_phone' => $data->phoneNumber,
                'onboarded'     => true, // setting this causes frontend to end onboarding
            ]);

            if ($data->expectedHostRequirements || $data->reasonForSignup) {
                $team->meta->onboard = [
                    'expectedHostRequirements' => $data->expectedHostRequirements,
                    'reasonForSignup'          => $data->reasonForSignup,
                    'onboardingTraining'       => $data->onboardingTraining,
                ];
            }

            $team->save();
        });

        $team->refresh();

        if ($team->meta->get('onboard.onboardingTraining', false)) {
            Notification::route('mail', config('cloudradar.support.support_email'))
                ->notify(new OnboardingCallConfirmationNotification(
                    $user, $team
                ));
        }
    }
}
