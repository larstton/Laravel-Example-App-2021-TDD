<?php

namespace Tests\Unit\Actions\SnmpCheck;

use App\Actions\SnmpCheck\UpdateSnmpCheckAction;
use App\Enums\CheckLastSuccess;
use App\Events\SnmpCheck\SnmpCheckUpdated;
use App\Models\SnmpCheck;
use Database\Factories\UpdateSnmpCheckDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateSnmpCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_snmp_check_using_supplied_data()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $snmpCheck = SnmpCheck::factory()->for($host)->create([
            'active'         => true,
            'check_interval' => 60,
            'last_success'   => CheckLastSuccess::Success(),
        ]);

        $data = UpdateSnmpCheckDataFactory::make([
            'checkInterval' => 120,
            'active'        => false,
        ]);

        $snmpCheck = resolve(UpdateSnmpCheckAction::class)->execute($snmpCheck, $data);

        $snmpCheck->refresh();

        $this->assertInstanceOf(SnmpCheck::class, $snmpCheck);
        $this->assertFalse($snmpCheck->active);
        $this->assertEquals(120, $snmpCheck->check_interval);
        $this->assertTrue($snmpCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertTrue($now->is($snmpCheck->updated_at));
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            SnmpCheckUpdated::class,
        ]);

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $snmpCheck = SnmpCheck::factory()->for($host)->create([
            'active'         => true,
            'check_interval' => 60,
            'last_success'   => CheckLastSuccess::Success(),
        ]);

        $data = UpdateSnmpCheckDataFactory::make([
            'checkInterval' => 120,
            'active'        => false,
        ]);

        resolve(UpdateSnmpCheckAction::class)->execute($snmpCheck, $data);

        Event::assertDispatched(SnmpCheckUpdated::class);
    }
}
