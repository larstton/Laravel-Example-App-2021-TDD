<?php

namespace Tests\Unit\Actions\Graph;

use App\Actions\Graph\BuildGraphDataByQueryBuilderAction;
use App\Http\Queries\InfluxQuery;
use App\Support\Influx\InfluxRepository;
use Illuminate\Foundation\Testing\WithoutEvents;
use Mockery\MockInterface;
use Tests\TestCase;

class BuildGraphDataByQueryBuilderActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_build_graph_data_and_return()
    {
        $influxQuery = $this->mock(InfluxQuery::class);

        $this->mock(InfluxRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive(
                'fetchByQueryBuilder->transformToGraphData->map->flattenIfOneHost->toArray'
            )->andReturn([
                0 => ['data0'],
                1 => ['data1'],
                2 => ['data2'],
            ]);
        });

        $data = resolve(BuildGraphDataByQueryBuilderAction::class)->execute($influxQuery);

        $this->assertEquals([
            0 => ['data0'],
            1 => ['data1'],
            2 => ['data2'],
        ], $data);
    }
}
