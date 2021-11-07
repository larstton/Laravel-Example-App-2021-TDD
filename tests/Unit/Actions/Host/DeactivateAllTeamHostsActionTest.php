<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\DeactivateAllTeamHostsAction;
use App\Enums\HostActiveState;
use App\Events\Host\HostUpdated;
use App\Models\Host;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeactivateAllTeamHostsActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_deactivate_all_hosts_for_given_team()
    {
        Event::fake([
            HostUpdated::class,
        ]);

        $team = $this->createTeam();

        Host::factory()->count(2)->create([
            'team_id' => $team->id,
            'active'  => HostActiveState::Active(),
        ]);

        resolve(DeactivateAllTeamHostsAction::class)->execute($team);

        $team->hosts->each(function (Host $host) {
            $this->assertEquals(HostActiveState::Deactivated, $host->active->value);
        });
        Event::assertDispatchedTimes(HostUpdated::class, 2);
    }
}
