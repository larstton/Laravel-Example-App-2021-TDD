<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\ActivateAllTeamHostsAction;
use App\Enums\HostActiveState;
use App\Events\Host\HostUpdated;
use App\Models\Host;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ActivateAllTeamHostsActionTest extends TestCase
{
    /** @test */
    public function will_activate_all_hosts_for_given_team()
    {
        Event::fake([
            HostUpdated::class,
        ]);

        $team = $this->createTeam();

        Host::factory()->count(2)->create([
            'team_id' => $team->id,
            'active'  => HostActiveState::Deactivated(),
        ]);

        resolve(ActivateAllTeamHostsAction::class)->execute($team);

        $team->hosts->each(function (Host $host) {
            $this->assertEquals(HostActiveState::Active, $host->active->value);
        });
        Event::assertDispatchedTimes(HostUpdated::class, 2);
    }
}
