<?php

namespace Tests\Unit\Actions\SubUnit;

use App\Actions\StatusPage\UpdateStatusPageAction;
use App\Actions\SubUnit\UpdateSubUnitAction;
use App\Models\StatusPage;
use App\Models\SubUnit;
use Database\Factories\StatusPageDataFactory;
use Database\Factories\SubUnitDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateSubUnitActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_sub_unit()
    {
        $team = $this->createTeam();

        $subUnit = SubUnit::factory()->for($team)->create([
            'short_id'     => 'old_short_id',
            'name'        => 'old_name',
            'information' => 'old_information',
        ]);

        $data = SubUnitDataFactory::make([
            'shortId'     => 'new_short_id',
            'name'        => 'new_name',
            'information' => 'new_information',
        ]);

        $subUnit = resolve(UpdateSubUnitAction::class)->execute($subUnit, $data);

        $this->assertInstanceOf(SubUnit::class, $subUnit);
        $this->assertEquals('new_short_id', $subUnit->short_id);
        $this->assertEquals('new_name', $subUnit->name);
        $this->assertEquals('new_information', $subUnit->information);
    }
}
