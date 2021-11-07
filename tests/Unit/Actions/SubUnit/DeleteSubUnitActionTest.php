<?php

namespace Tests\Unit\Actions\SubUnit;

use App\Actions\SubUnit\DeleteSubUnitAction;
use App\Models\Host;
use App\Models\SubUnit;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class DeleteSubUnitActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_sub_unit()
    {
        $team = $this->createTeam();
        $subUnit = SubUnit::factory()->for($team)->create();

        resolve(DeleteSubUnitAction::class)->execute($subUnit);

        $this->assertDeleted($subUnit);
    }

    /** @test */
    public function will_remove_sub_unit_from_hosts_if_attached()
    {
        $team = $this->createTeam();
        $subUnit = SubUnit::factory()->for($team)->create();
        /** @var Host $host */
        $host = Host::factory()->for($team)->for($subUnit)->create();

        $this->assertNotNull($host->sub_unit_id);

        resolve(DeleteSubUnitAction::class)->execute($subUnit);

        $host->refresh();

        $this->assertNull($host->sub_unit_id);
    }
}
