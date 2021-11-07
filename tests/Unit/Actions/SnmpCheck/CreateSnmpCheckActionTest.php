<?php

namespace Tests\Unit\Actions\SnmpCheck;

use App\Actions\SnmpCheck\CreateSnmpCheckAction;
use App\Enums\CheckLastSuccess;
use App\Events\SnmpCheck\SnmpCheckCreated;
use App\Models\SnmpCheck;
use Database\Factories\CreateSnmpCheckDataFactory;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateSnmpCheckActionTest extends TestCase
{
    /** @test */
    public function can_create_snmp_check_for_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = CreateSnmpCheckDataFactory::make([
            'preset'        => 'basedata',
            'checkInterval' => 60,
            'active'        => true,
        ]);

        $snmpCheck = resolve(CreateSnmpCheckAction::class)->execute($user, $host, $data);

        $snmpCheck->refresh();

        $this->assertInstanceOf(SnmpCheck::class, $snmpCheck);
        $this->assertEquals($host->id, $snmpCheck->host_id);
        $this->assertEquals($user->id, $snmpCheck->user_id);
        $this->assertEquals('basedata', $snmpCheck->preset);
        $this->assertEquals(60, $snmpCheck->check_interval);
        $this->assertTrue($snmpCheck->active);
        $this->assertTrue($snmpCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertNull($snmpCheck->last_message);
        $this->assertNull($snmpCheck->last_checked_at);
    }

    /** @test */
    public function will_dispatch_created_event()
    {
        Event::fake([
            SnmpCheckCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = CreateSnmpCheckDataFactory::make([
            'preset'        => 'basedata',
            'checkInterval' => 60,
            'active'        => true,
        ]);

        resolve(CreateSnmpCheckAction::class)->execute($user, $host, $data);

        Event::assertDispatched(SnmpCheckCreated::class);
    }
}
