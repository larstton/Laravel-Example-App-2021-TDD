<?php

namespace Tests\Unit\Actions\SubUnit;

use App\Actions\SubUnit\CreateSubUnitAction;
use App\Models\SubUnit;
use Database\Factories\SubUnitDataFactory;
use Tests\TestCase;

class CreateSubUnitActionTest extends TestCase
{
    /** @test */
    public function can_create_new_sub_unit()
    {
        $team = $this->createTeam();

        $data = SubUnitDataFactory::make([
            'shortId'     => 'short_id',
            'name'        => 'name',
            'information' => 'information',
        ]);

        $subUnit = resolve(CreateSubUnitAction::class)->execute($data);

        $subUnit->refresh();

        $this->assertInstanceOf(SubUnit::class, $subUnit);
        $this->assertEquals($team->id, $subUnit->team_id);
        $this->assertEquals('short_id', $subUnit->short_id);
        $this->assertEquals('name', $subUnit->name);
        $this->assertEquals('information', $subUnit->information);
    }
}
