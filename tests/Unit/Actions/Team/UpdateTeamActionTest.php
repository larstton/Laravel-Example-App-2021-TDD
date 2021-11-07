<?php

namespace Actions\Team;

use App\Actions\Team\UpdateTeamAction;
use App\Models\Frontman;
use App\Models\Team;
use Database\Factories\UpdateTeamDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateTeamActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_team()
    {
        $team = $this->createTeam([
            'name'                          => 'name',
            'timezone'                      => 'UTC',
            'default_frontman_id'           => null,
            'date_format'                   => 'M/',
            'has_granted_access_to_support' => true,
        ]);
        $frontman = Frontman::factory()->for($team)->create();

        $data = UpdateTeamDataFactory::make([
            'name'                      => 'new-name',
            'timezone'                  => 'GMT',
            'defaultFrontman'           => $frontman,
            'dateFormat'                => 'L.',
            'hasGrantedAccessToSupport' => false,
        ]);

        $team = resolve(UpdateTeamAction::class)->execute($team, $data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('new-name', $team->name);
        $this->assertEquals('GMT', $team->timezone);
        $this->assertEquals($frontman->id, $team->default_frontman_id);
        $this->assertEquals('L.', $team->date_format);
        $this->assertFalse($team->has_granted_access_to_support);
    }

    /** @test */
    public function will_use_original_values_if_not_present_in_dto()
    {
        $team = $this->createTeam([
            'name'                          => 'name',
            'timezone'                      => 'UTC',
            'default_frontman_id'           => '169502d5-a541-49bb-9782-4cf4d71148cf',
            'date_format'                   => 'M/',
            'has_granted_access_to_support' => true,
        ]);

        $data = UpdateTeamDataFactory::make([
            'name'                      => null,
            'timezone'                  => null,
            'defaultFrontman'           => null,
            'dateFormat'                => null,
            'hasGrantedAccessToSupport' => null,
        ])->setHasData([
            'name'                      => false,
            'timezone'                  => false,
            'defaultFrontman'           => false,
            'dateFormat'                => false,
            'hasGrantedAccessToSupport' => false,
        ]);

        $team = resolve(UpdateTeamAction::class)->execute($team, $data);

        $this->assertEquals('name', $team->name);
        $this->assertEquals('UTC', $team->timezone);
        $this->assertEquals('169502d5-a541-49bb-9782-4cf4d71148cf', $team->default_frontman_id);
        $this->assertEquals('M/', $team->date_format);
        $this->assertTrue($team->has_granted_access_to_support);
    }
}
