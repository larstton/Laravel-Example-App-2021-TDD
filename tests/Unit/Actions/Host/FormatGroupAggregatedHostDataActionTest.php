<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\FormatGroupAggregatedHostDataAction;
use App\Data\Host\AggregatedHostDataData;
use App\Models\Host;
use App\Models\Tag;
use App\Support\NotifierService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Tests\TestCase;

class FormatGroupAggregatedHostDataActionTest extends TestCase
{
    /** @test */
    public function will_format_aggregated_host_data_when_grouped_by_tag()
    {
        $team = $this->createTeam();

        Host::factory()->hasAttached(
            Tag::findOrCreate(['host-tag-1'], 'host')
        )->hasWebChecks(3)->create([
            'team_id'                => $team->id,
            'cagent_last_updated_at' => now(),
            'cagent_metrics'         => 10,
        ]);
        Host::factory()->hasAttached(
            $tags = Tag::findOrCreate(['host-tag-1', 'host-tag-2'], 'host')
        )->hasWebChecks(2)->create([
            'team_id'                => $team->id,
            'cagent_last_updated_at' => now()->subHours(2),
            'cagent_metrics'         => 20,
        ]);

        $tags->each->load('hosts');
        $tags->each(function (Tag $tag) {
            $tag->hosts->map(function (Host $host) {
                $host->alert_count = 8;
                $host->warning_count = 12;
            });
        });

        $lengthAwarePaginator = new LengthAwarePaginator($tags, 1, 10);

        $data = resolve(FormatGroupAggregatedHostDataAction::class)->execute($lengthAwarePaginator);

        $this->assertInstanceOf(LengthAwarePaginator::class, $data);

        /** @var AggregatedHostDataData $data1 */
        $data1 = $data[0];
        $this->assertInstanceOf(AggregatedHostDataData::class, $data1);
        $this->assertEquals('group', $data1->groupedBy);
        $this->assertEquals('host-tag-1', $data1->groupedEntity);
        $this->assertEquals(2, $data1->hostsCount);
        $this->assertEquals(5, $data1->checkCount);
        $this->assertEquals(1, $data1->connectedAgents);
        $this->assertEquals(1, $data1->disconnectedAgents);
        $this->assertEquals(30, $data1->metrics);
        $this->assertEquals(16, $data1->alerts);
        $this->assertEquals(24, $data1->warnings);

        /** @var AggregatedHostDataData $data2 */
        $data2 = $data[1];
        $this->assertInstanceOf(AggregatedHostDataData::class, $data2);
        $this->assertEquals('group', $data2->groupedBy);
        $this->assertEquals('host-tag-2', $data2->groupedEntity);
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

        Tag::findOrCreate(['group:group-1'], 'host');

        Host::factory()->hasAttached(
            Tag::findOrCreate(['group:group-2'], 'host')
        )->hasWebChecks(3)->create([
            'team_id'                => $team->id,
            'cagent_last_updated_at' => now(),
            'cagent_metrics'         => 10,
        ]);

        $tags = Tag::all();

        $tags->each->load('hosts');
        $tags->each(function (Tag $tag) {
            $tag->hosts->map(function (Host $host) {
                $host->alert_count = 8;
                $host->warning_count = 12;
            });
        });

        $lengthAwarePaginator = new LengthAwarePaginator($tags, 1, 10);

        $data = resolve(FormatGroupAggregatedHostDataAction::class)->execute($lengthAwarePaginator);

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
