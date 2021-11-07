<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Event\PurgeEventsForHostAction;
use App\Actions\Host\LogHostHistoryAction;
use App\Actions\Host\PostHostDeleteTidyUpAction;
use App\Enums\EventState;
use App\Enums\Rule\RuleHostMatchPart;
use App\Events\CustomCheck\CustomCheckDeleted;
use App\Events\Rule\RuleDeleted;
use App\Events\ServiceCheck\ServiceCheckDeleted;
use App\Events\SnmpCheck\SnmpCheckDeleted;
use App\Events\WebCheck\WebCheckDeleted;
use App\Models\CheckResult;
use App\Models\CustomCheck;
use App\Models\Host;
use App\Models\Rule;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\Team;
use App\Models\User;
use App\Models\WebCheck;
use App\Support\Influx\InfluxRepository;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class PostHostDeleteTidyUpActionTest extends TestCase
{
    use WithoutEvents;

    private Host $host;
    private Team $team;
    private User $user;
    private WebCheck $webCheck;
    private ServiceCheck $serviceCheck;
    private SnmpCheck $snmpCheck;
    private CustomCheck $customCheck;
    private Rule $rule;
    private Rule $rule1;
    private Rule $rule2;
    private Rule $rule3;
    private Rule $rule4;
    private Rule $rule5;

    /** @test */
    public function will_tidy_up_entities_after_deleting_host()
    {
        $this->buildHostEntities();

        $this->mock(PurgeEventsForHostAction::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute', [$this->team, $this->host->id])->andReturnUndefined();
        });

        $this->mock(InfluxRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('setDatabase->dropMeasurement')->andReturnUndefined();
        });

        $this->mock(NotifierService::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteHost', [
                $this->host, true,
            ])->andReturnTrue();
        });

        $this->mock(LogHostHistoryAction::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute', [
                $this->host, 'delete',
            ])->andReturnUndefined();
        });

        resolve(PostHostDeleteTidyUpAction::class)->execute($this->host, $this->user, true);

        $this->assertDatabaseMissing('check_results', [
            'check_id' => $this->host->id,
        ]);
        $this->assertDatabaseMissing('check_results', [
            'check_id' => $this->webCheck->id,
        ]);
        $this->assertDatabaseMissing('check_results', [
            'check_id' => $this->serviceCheck->id,
        ]);
        $this->assertDatabaseMissing('check_results', [
            'check_id' => $this->snmpCheck->id,
        ]);
        $this->assertDatabaseMissing('check_results', [
            'check_id' => $this->customCheck->id,
        ]);
        $this->assertDatabaseMissing('web_checks', [
            'host_id' => $this->host->id,
        ]);
        $this->assertDatabaseMissing('service_checks', [
            'host_id' => $this->host->id,
        ]);
        $this->assertDatabaseMissing('custom_checks', [
            'host_id' => $this->host->id,
        ]);
        $this->assertDatabaseMissing('snmp_checks', [
            'host_id' => $this->host->id,
        ]);

        $this->assertDeleted($this->webCheck);
        $this->assertDeleted($this->serviceCheck);
        $this->assertDeleted($this->snmpCheck);
        $this->assertDeleted($this->customCheck);
        $this->assertDeleted($this->rule);
        $this->assertDeleted($this->rule1);
        $this->assertDeleted($this->rule2);
        $this->assertDeleted($this->rule3);
        $this->assertDeleted($this->rule4);
        $this->assertDeleted($this->rule5);

        Event::assertDispatched(WebCheckDeleted::class);
        Event::assertDispatched(ServiceCheckDeleted::class);
        Event::assertDispatched(SnmpCheckDeleted::class);
        Event::assertDispatched(CustomCheckDeleted::class);
        Event::assertDispatchedTimes(RuleDeleted::class, 6);
    }

    private function buildHostEntities()
    {
        $this->team = $this->createTeam();
        $this->user = $this->createUser([
            'team_id' => $this->team->id,
        ], false);

        $this->host = $this->createHost([
            'team_id' => $this->team->id,
            'cagent'  => true,
        ]);
        CheckResult::factory()
            ->for($this->host)
            ->create([
                'check_id' => $this->host->id,
            ]);

        $this->webCheck = WebCheck::factory()
            ->for($this->host)
            ->create();
        CheckResult::factory()
            ->for($this->host)
            ->for($this->webCheck, 'check')
            ->create();

        $this->serviceCheck = ServiceCheck::factory()
            ->for($this->host)
            ->create();
        CheckResult::factory()
            ->for($this->host)
            ->for($this->serviceCheck, 'check')
            ->create();

        $this->snmpCheck = SnmpCheck::factory()
            ->for($this->host)
            ->create();
        CheckResult::factory()
            ->for($this->host)
            ->for($this->snmpCheck, 'check')
            ->create();

        $this->customCheck = CustomCheck::factory()
            ->for($this->host)
            ->create();
        CheckResult::factory()
            ->for($this->host)
            ->for($this->customCheck, 'check')
            ->create();

        $this->rule = Rule::factory()
            ->for($this->team)
            ->create([
                'host_match_criteria' => $this->host->id,
                'host_match_part'     => RuleHostMatchPart::UUID(),
            ]);

        $this->rule1 = Rule::factory()->for($this->team)->makeDiskFullAlertRule()->create([
            'checksum' => md5(Str::random()),
        ]);
        tap($this->rule1)->calculateChecksum()->save();
        $this->rule2 = Rule::factory()->for($this->team)->makeLowMemoryWarningRule()->create([
            'checksum' => md5(Str::random()),
        ]);
        tap($this->rule2)->calculateChecksum()->save();
        $this->rule3 = Rule::factory()->for($this->team)->makeCPUHighLoadWarningRule()->create([
            'checksum' => md5(Str::random()),
        ]);
        tap($this->rule3)->calculateChecksum()->save();
        $this->rule4 = Rule::factory()->for($this->team)->makeModuleAlertRule()->create([
            'checksum' => md5(Str::random()),
        ]);
        tap($this->rule4)->calculateChecksum()->save();
        $this->rule5 = Rule::factory()->for($this->team)->makeModuleWarningRule()->create([
            'checksum' => md5(Str::random()),
        ]);
        tap($this->rule5)->calculateChecksum()->save();
    }

    /** @test */
    public function will_mark_events_as_resolved_if_not_purging_reports()
    {
        Carbon::setTestNow($now = now());

        $this->buildHostEntities();

        $this->mock(InfluxRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('setDatabase->dropMeasurement')->andReturnUndefined();
        });

        \App\Models\Event::factory()
            ->for($this->host)
            ->for($this->team)
            ->create([
                'check_id'    => $this->webCheck->id,
                'state'       => EventState::Active(),
                'resolved_at' => null,
            ]);

        resolve(PostHostDeleteTidyUpAction::class)->execute($this->host, $this->user, false);

        $this->assertDatabaseHas('events', [
            'team_id'     => $this->team->id,
            'host_id'     => $this->host->id,
            'state'       => EventState::Recovered(),
            'resolved_at' => $now,
        ]);
    }
}
