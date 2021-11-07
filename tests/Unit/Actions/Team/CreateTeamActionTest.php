<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\CreateTeamAction;
use App\Enums\TeamPlan;
use App\Models\Frontman;
use App\Models\Team;
use Database\Factories\CreateTeamDataFactory;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreateTeamActionTest extends TestCase
{
    /** @test */
    public function can_create_team()
    {
        $data = CreateTeamDataFactory::make([
            'trialEnd'          => $trialEnds = now()->addDays(7),
            'partner'           => null,
            'partnerExtraData'  => null,
            'registrationTrack' => [
                'ctrack' => json_encode(['hello']),
                'ga'     => 'ga_cookie',
            ],
        ]);

        $team = resolve(CreateTeamAction::class)->execute($data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('NOTSET', $team->timezone);
        $this->assertEquals(Frontman::DEFAULT_FRONTMAN_UUID, $team->default_frontman_id);
        $this->assertEquals(30, $team->data_retention);
        $this->assertEquals(999, $team->max_hosts);
        $this->assertEquals(99, $team->max_frontmen);
        $this->assertEquals(99, $team->max_members);
        $this->assertTrue($team->plan->is(TeamPlan::Trial()));
        $this->assertEquals(60, $team->min_check_interval);
        $this->assertDateTimesMatch($team->trial_ends_at, $trialEnds);
        $this->assertNull($team->partner);
        $this->assertNull($team->partner_extra_data);
        $this->assertEquals('{"ga":"ga_cookie","0":"hello"}', $team->registration_track);
    }

    /** @test */
    public function trial_will_default_if_not_set()
    {
        Carbon::setTestNow($now = now());

        $data = CreateTeamDataFactory::make([
            'trialEnd' => null,
        ]);

        $team = resolve(CreateTeamAction::class)->execute($data);

        $this->assertDateTimesMatch($team->trial_ends_at, $now->addDays(15));
    }

    /** @test */
    public function trial_will_default_if_not_set_when_partner_is_plesk()
    {
        Carbon::setTestNow($now = now());

        $data = CreateTeamDataFactory::make([
            'trialEnd' => null,
            'partner'  => 'plesk',
        ]);

        $team = resolve(CreateTeamAction::class)->execute($data);

        $this->assertDateTimesMatch($team->trial_ends_at, $now->addDays(30));
        $this->assertEquals('plesk', $team->partner);
    }
}
