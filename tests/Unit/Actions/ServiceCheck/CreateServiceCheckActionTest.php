<?php

namespace Tests\Unit\Actions\ServiceCheck;

use App\Actions\ServiceCheck\CreateServiceCheckAction;
use App\Enums\CheckLastSuccess;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleOperator;
use App\Events\Rule\RuleCreated;
use App\Events\ServiceCheck\ServiceCheckCreated;
use App\Models\Rule;
use App\Models\ServiceCheck;
use App\Support\Preflight\Contract\CheckPreflight;
use Database\Factories\ServiceCheckDataFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateServiceCheckActionTest extends TestCase
{
    private $checkPreflight;

    /** @test */
    public function can_create_service_check_for_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol'      => 'tcp',
            'checkInterval' => 60,
            'service'       => 'https',
            'port'          => 80,
            'active'        => true,
            'preflight'     => false,
        ]);

        $serviceCheck = resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        $serviceCheck->refresh();

        $this->assertInstanceOf(ServiceCheck::class, $serviceCheck);
        $this->assertEquals($host->id, $serviceCheck->host_id);
        $this->assertEquals($user->id, $serviceCheck->user_id);
        $this->assertEquals('tcp', $serviceCheck->protocol);
        $this->assertEquals(60, $serviceCheck->check_interval);
        $this->assertEquals('https', $serviceCheck->service);
        $this->assertEquals(80, $serviceCheck->port);
        $this->assertTrue($serviceCheck->active);
        $this->assertEquals(0, $serviceCheck->in_progress);
        $this->assertTrue($serviceCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertNull($serviceCheck->last_message);
        $this->assertNull($serviceCheck->last_checked_at);
    }

    /** @test */
    public function will_dispatch_created_event()
    {
        Event::fake([
            ServiceCheckCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make();

        resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        Event::assertDispatched(ServiceCheckCreated::class);
    }

    /** @test */
    public function will_perform_preflight()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'preflight' => true,
        ]);

        $this->checkPreflight->shouldReceive('serviceCheck', [$host, $data])
            ->andReturnTrue();

        resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);
    }

    /** @test */
    public function will_force_check_interval_to_3600_for_ssl_protocol()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol'      => 'ssl',
            'checkInterval' => 60,
            'service'       => 'https',
            'port'          => 80,
            'active'        => true,
            'preflight'     => false,
        ]);

        $serviceCheck = resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        $serviceCheck->refresh();

        $this->assertEquals('ssl', $serviceCheck->protocol);
        $this->assertEquals(3600, $serviceCheck->check_interval);
    }

    /** @test */
    public function will_set_port_to_zero_if_null()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol'      => 'tcp',
            'checkInterval' => 60,
            'service'       => 'https',
            'port'          => null,
            'active'        => true,
            'preflight'     => false,
        ]);

        $serviceCheck = resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        $serviceCheck->refresh();

        $this->assertEquals(0, $serviceCheck->port);
    }

    /** @test */
    public function will_make_icmp_round_trip_rule_if_service_is_ping()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol' => 'icmp',
            'service'  => 'ping',
        ]);

        resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'       => $user->team_id,
            'action'        => RuleAction::Warn(),
            'check_type'    => RuleCheckType::ServiceCheck(),
            'function'      => RuleFunction::Average(),
            'operator'      => RuleOperator::GreaterThan(),
            'check_key'     => 'net.icmp.ping.round_trip_time_s',
            'threshold'     => 1,
            'results_range' => 5,
            'updated_at'    => $now,
            'created_at'    => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_make_packet_loss_rule_if_service_is_ping()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol' => 'icmp',
            'service'  => 'ping',
        ]);

        resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'       => $user->team_id,
            'action'        => RuleAction::Alert(),
            'check_type'    => RuleCheckType::ServiceCheck(),
            'function'      => RuleFunction::Average(),
            'operator'      => RuleOperator::GreaterThan(),
            'check_key'     => 'net.icmp.ping.packetLoss_percent',
            'threshold'     => 85,
            'results_range' => 5,
            'updated_at'    => $now,
            'created_at'    => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function wont_create_rules_if_they_exist()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = ServiceCheckDataFactory::make([
            'protocol' => 'icmp',
            'service'  => 'ping',
        ]);

        Event::fakeFor(function () use ($team) {
            tap(
                Rule::factory()->makeICMPRoundTripAlertRule()->for($team)->make()
            )->calculateChecksum()->save();
            tap(
                Rule::factory()->makeICMPPacketLossAlertRule()->for($team)->make()
            )->calculateChecksum()->save();
        });

        resolve(CreateServiceCheckAction::class)->execute($user, $host, $data);

        Event::assertNotDispatched(RuleCreated::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPreflight = $this->mock(CheckPreflight::class)->shouldIgnoreMissing();
    }
}
