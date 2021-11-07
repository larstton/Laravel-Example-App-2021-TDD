<?php

namespace Tests\Unit\Actions\SnmpCheck;

use App\Actions\SnmpCheck\DeleteSnmpCheckAction;
use App\Events\SnmpCheck\SnmpCheckDeleted;
use App\Jobs\SnmpCheck\DeleteSnmpCheck;
use App\Models\SnmpCheck;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteSnmpCheckActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_snmp_check()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $snmpCheck = SnmpCheck::factory()->create();

        resolve(DeleteSnmpCheckAction::class)->execute($user, $snmpCheck, $host);

        $this->assertDeleted($snmpCheck);
    }

    /** @test */
    public function will_dispatch_deleted_event()
    {
        Event::fake([
            SnmpCheckDeleted::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $snmpCheck = SnmpCheck::factory()->create();

        resolve(DeleteSnmpCheckAction::class)->execute($user, $snmpCheck, $host);

        Event::assertDispatched(SnmpCheckDeleted::class);
    }

    /** @test */
    public function will_dispatch_job()
    {
        Bus::fake([
            DeleteSnmpCheck::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);
        $snmpCheck = SnmpCheck::factory()->create();

        resolve(DeleteSnmpCheckAction::class)->execute($user, $snmpCheck, $host);

        Bus::assertDispatched(function (DeleteSnmpCheck $job) use ($host, $snmpCheck, $user) {
            $this->assertTrue($user->is($job->user));
            $this->assertTrue($snmpCheck->is($job->snmpCheck));
            $this->assertTrue($host->is($job->host));

            return true;
        });
    }
}
