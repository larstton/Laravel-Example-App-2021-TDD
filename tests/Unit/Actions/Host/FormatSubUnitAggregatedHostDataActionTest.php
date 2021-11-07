<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\FormatSubUnitAggregatedHostDataAction;
use App\Data\Host\AggregatedHostDataData;
use App\Models\Host;
use App\Models\SubUnit;
use App\Support\NotifierService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Tests\TestCase;

class FormatSubUnitAggregatedHostDataActionTest extends TestCase
{
    /** @test */
    public function will_format_aggregated_host_data_when_grouped_by_sub_unit()
    {
        $team = $this->createTeam();

        $subUnit1 = SubUnit::factory()->create([
            'team_id'  => $team->id,
            'short_id' => 'short_1',
            'name'     => 'name_1',
        ]);
        Host::factory()->hasWebChecks(3)->create([
            'team_id'                => $team->id,
            'sub_unit_id'            => $subUnit1->id,
            'cagent_last_updated_at' => now(),
            'cagent_metrics'         => 10,
        ]);
        Host::factory()->hasWebChecks(3)->create([
            'team_id'                => $team->id,
            'sub_unit_id'            => $subUnit1->id,
            'cagent_last_updated_at' => now(),
            'cagent_metrics'         => 2,
        ]);

        $subUnit2 = SubUnit::factory()->create([
            'team_id'  => $team->id,
            'short_id' => 'short_2',
            'name'     => 'name_2',
        ]);
        Host::factory()->hasWebChecks(2)->create([
            'team_id'                => $team->id,
            'sub_unit_id'            => $subUnit2->id,
            'cagent_last_updated_at' => now()->subHours(2),
            'cagent_metrics'         => 20,
        ]);

        $subUnits = $team->subUnits;

        $subUnits->each->load('hosts');
        $subUnits->each(function (SubUnit $subUnit) {
            $subUnit->hosts->map(function (Host $host) {
                $host->alert_count = 8;
                $host->warning_count = 12;
            });
        });

        $lengthAwarePaginator = new LengthAwarePaginator($subUnits, 1, 10);

        $data = resolve(FormatSubUnitAggregatedHostDataAction::class)->execute($lengthAwarePaginator);

        $this->assertInstanceOf(LengthAwarePaginator::class, $data);

        /** @var AggregatedHostDataData $data1 */
        $data1 = $data[0];
        $this->assertInstanceOf(AggregatedHostDataData::class, $data1);
        $this->assertEquals('sub-unit', $data1->groupedBy);
        $this->assertEquals([
            'id'      => $subUnit1->id,
            'shortId' => $subUnit1->short_id,
        ], $data1->groupedEntity);
        $this->assertEquals(2, $data1->hostsCount);
        $this->assertEquals(6, $data1->checkCount);
        $this->assertEquals(2, $data1->connectedAgents);
        $this->assertEquals(0, $data1->disconnectedAgents);
        $this->assertEquals(12, $data1->metrics);
        $this->assertEquals(16, $data1->alerts);
        $this->assertEquals(24, $data1->warnings);

        /** @var AggregatedHostDataData $data2 */
        $data2 = $data[1];
        $this->assertInstanceOf(AggregatedHostDataData::class, $data2);
        $this->assertEquals('sub-unit', $data2->groupedBy);
        $this->assertEquals([
            'id'      => $subUnit2->id,
            'shortId' => $subUnit2->short_id,
        ], $data2->groupedEntity);
        $this->assertEquals(1, $data2->hostsCount);
        $this->assertEquals(2, $data2->checkCount);
        $this->assertEquals(0, $data2->connectedAgents);
        $this->assertEquals(1, $data2->disconnectedAgents);
        $this->assertEquals(20, $data2->metrics);
        $this->assertEquals(8, $data2->alerts);
        $this->assertEquals(12, $data2->warnings);
    }

    /** @test */
    public function will_remove_aggregated_unit_if_under_lying_host_count_is_zero()
    {
        $team = $this->createTeam();

        SubUnit::factory()->create([
            'team_id'  => $team->id,
            'short_id' => 'short_1',
            'name'     => 'name_1',
        ]);
        $subUnit = SubUnit::factory()->create([
            'team_id'  => $team->id,
            'short_id' => 'short_2',
            'name'     => 'name_2',
        ]);
        Host::factory()->create([
            'team_id'                => $team->id,
            'sub_unit_id'            => $subUnit->id,
            'cagent_last_updated_at' => now(),
            'cagent_metrics'         => 10,
        ]);

        $subUnits = $team->subUnits;

        $subUnits->each->load('hosts');
        $subUnits->each(function (SubUnit $subUnit) {
            $subUnit->hosts->map(function (Host $host) {
                $host->alert_count = 8;
                $host->warning_count = 12;
            });
        });

        $lengthAwarePaginator = new LengthAwarePaginator($subUnits, 1, 10);

        $data = resolve(FormatSubUnitAggregatedHostDataAction::class)->execute($lengthAwarePaginator);

        $this->assertCount(1, $data);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(NotifierService::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();
        });
    }
}
