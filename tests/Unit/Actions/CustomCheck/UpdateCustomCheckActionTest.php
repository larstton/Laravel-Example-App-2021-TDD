<?php

namespace Tests\Unit\Actions\CustomCheck;

use App\Actions\CustomCheck\UpdateCustomCheckAction;
use App\Data\CustomCheck\CustomCheckData;
use App\Events\CustomCheck\CustomCheckUpdated;
use App\Models\CustomCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateCustomCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_custom_check_using_supplied_data()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $customCheck = CustomCheck::factory()->for($host)->create([
            'name'                     => 'name1',
            'expected_update_interval' => 500,
        ]);

        $data = new CustomCheckData([
            'name'                   => 'name2',
            'expectedUpdateInterval' => 1000,
        ]);

        $customCheck = resolve(UpdateCustomCheckAction::class)->execute($customCheck, $data);

        $this->assertInstanceOf(CustomCheck::class, $customCheck);
        $this->assertEquals('name2', $customCheck->name);
        $this->assertEquals(1000, $customCheck->expected_update_interval);
        $this->assertTrue($now->is($customCheck->updated_at));
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            CustomCheckUpdated::class,
        ]);

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $customCheck = CustomCheck::factory()->for($host)->create();

        $data = new CustomCheckData([
            'name'                   => $this->faker->name,
            'expectedUpdateInterval' => 10,
        ]);

        resolve(UpdateCustomCheckAction::class)->execute($customCheck, $data);

        Event::assertDispatched(CustomCheckUpdated::class);
    }
}
