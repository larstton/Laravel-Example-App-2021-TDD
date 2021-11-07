<?php

namespace Tests\Unit\Actions\Onboard;

use App\Actions\Onboard\SaveOnboardingPayloadStep1Action;
use App\Data\Onboard\OnboardStep1Data;
use App\Models\Frontman;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SaveOnboardingPayloadStep1ActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_team_with_data_from_step_1_onboarding_flow()
    {
        $team = $this->createTeam();

        $data = new OnboardStep1Data([
            'timezone'        => 'Europe/London',
            'defaultFrontman' => Frontman::find('24995c49-45ba-43d6-9205-4f5e83d32a11'),
            'dateFormat'      => 'L/',
        ]);

        resolve(SaveOnboardingPayloadStep1Action::class)->execute($data, $team);

        $team->refresh();

        $this->assertEquals('Europe/London', $team->timezone);
        $this->assertEquals('L/', $team->date_format);
        $this->assertEquals('24995c49-45ba-43d6-9205-4f5e83d32a11', $team->default_frontman_id);
    }
}
