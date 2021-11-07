<?php

namespace Tests\Unit\Actions\Onboard;

use App\Actions\Onboard\SaveOnboardingPayloadStep2Action;
use App\Data\Onboard\OnboardStep2Data;
use App\Notifications\Onboard\OnboardingCallConfirmationNotification;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SaveOnboardingPayloadStep2ActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_team_with_data_from_step_2_onboarding_flow()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = new OnboardStep2Data([
            'name'                     => 'user-name',
            'companyName'              => 'company-name',
            'phoneNumber'              => '01234567899',
            'expectedHostRequirements' => '11-50',
            'reasonForSignup'          => 'I want to set up our first monitoring tool',
            'onboardingTraining'       => false,
        ]);

        resolve(SaveOnboardingPayloadStep2Action::class)->execute($data, $user, $team);

        $team->refresh();

        $this->assertEquals('user-name', $user->name);
        $this->assertEquals('company-name', $team->company_name);
        $this->assertEquals('01234567899', $team->company_phone);
        $this->assertTrue($team->onboarded);
        $this->assertEquals([
            'expectedHostRequirements' => '11-50',
            'reasonForSignup'          => 'I want to set up our first monitoring tool',
            'onboardingTraining'       => false,
        ], $team->meta->onboard);
    }

    /** @test */
    public function will_persist_data_if_onboarding_survey_meta_is_missing()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = new OnboardStep2Data([
            'name'                     => 'user-name',
            'companyName'              => 'company-name',
            'phoneNumber'              => '01234567899',
            'expectedHostRequirements' => null,
            'reasonForSignup'          => null,
            'onboardingTraining'       => false,
        ]);

        resolve(SaveOnboardingPayloadStep2Action::class)->execute($data, $user, $team);

        $team->refresh();

        $this->assertNull($team->meta->onboard);
    }

    /** @test */
    public function will_send_notification_if_onboarding_call_checked()
    {
        Notification::fake();

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = new OnboardStep2Data([
            'name'                     => 'user-name',
            'companyName'              => 'company-name',
            'phoneNumber'              => '01234567899',
            'expectedHostRequirements' => '51-100',
            'reasonForSignup'          => 'I want to set up our first monitoring tool',
            'onboardingTraining'       => true,
        ]);

        resolve(SaveOnboardingPayloadStep2Action::class)->execute($data, $user, $team);

        $team->refresh();

        $this->assertEquals([
            'expectedHostRequirements' => '51-100',
            'reasonForSignup'          => 'I want to set up our first monitoring tool',
            'onboardingTraining'       => true,
        ], $team->meta->onboard);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            OnboardingCallConfirmationNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === config('cloudradar.support.support_email');
            }
        );
    }
}
