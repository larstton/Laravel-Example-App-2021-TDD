<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\CreateHostAction;
use App\Enums\HostActiveState;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Events\Host\HostCreated;
use App\Events\Rule\RuleCreated;
use App\Exceptions\HostException;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\SubUnit;
use App\Support\NotifierService;
use Database\Factories\HostDataFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateHostActionTest extends TestCase
{
    /** @test */
    public function will_create_new_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'name'        => 'host-name',
            'description' => 'host-description',
            'connect'     => '8.8.8.8',
            'cagent'      => false,
            'dashboard'   => true,
            'muted'       => false,
            'active'      => true,
            'frontman'    => null,
            'subUnit'     => null,
            'tags'        => null,
            'snmpData'    => [],
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertInstanceOf(Host::class, $host);
        $this->assertEquals('host-name', $host->name);
        $this->assertEquals($user->team_id, $host->team_id);
        $this->assertEquals($user->team->default_frontman_id, $host->frontman_id);
        $this->assertNull($host->sub_unit_id);
        $this->assertEquals('host-description', $host->description);
        $this->assertTrue($host->active->is(HostActiveState::Active()));
        $this->assertEquals($user->id, $host->user_id);
        $this->assertNull($host->last_update_user_id);
        $this->assertNotEmpty($host->password);
        $this->assertEquals('8.8.8.8', $host->connect);
        $this->assertFalse($host->cagent);
        $this->assertNull($host->cagent_last_updated_at);
        $this->assertNull($host->snmp_check_last_updated_at);
        $this->assertNull($host->web_check_last_updated_at);
        $this->assertNull($host->service_check_last_updated_at);
        $this->assertNull($host->custom_check_last_updated_at);
        $this->assertNull($host->inventory);
        $this->assertEquals(0, $host->cagent_metrics);
        $this->assertTrue($host->dashboard);
        $this->assertFalse($host->muted);
        $this->assertNull($host->hw_inventory);
        $this->assertNull($host->snmp_username);
        $this->assertNull($host->snmp_protocol);
        $this->assertNull($host->snmp_port);
        $this->assertNull($host->snmp_community);
        $this->assertNull($host->snmp_timeout);
        $this->assertNull($host->snmp_privacy_protocol);
        $this->assertNull($host->snmp_security_level);
        $this->assertNull($host->snmp_authentication_protocol);
        $this->assertNull($host->snmp_authentication_password);
        $this->assertNull($host->snmp_privacy_password);
        $this->assertEmpty($host->tags);
    }

    /** @test */
    public function can_create_new_host_with_user_as_authed_entity()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make();

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertEquals($hostData->name, $host->name);
        $this->assertEquals($hostData->connect, $host->connect);
        $this->assertDatabaseHas('hosts', [
            'name'    => $hostData->name,
            'connect' => $hostData->connect,
        ]);
    }

    /** @test */
    public function can_create_new_host_with_api_token_as_authed_entity()
    {
        $apiToken = $this->createApiToken();
        $hostData = HostDataFactory::make();

        $host = resolve(CreateHostAction::class)->execute($apiToken, $hostData);

        $this->assertEquals($hostData->name, $host->name);
        $this->assertEquals($hostData->connect, $host->connect);
        $this->assertDatabaseHas('hosts', [
            'name'    => $hostData->name,
            'connect' => $hostData->connect,
        ]);
    }

    /** @test */
    public function can_set_frontman_when_creating_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'frontman' => Frontman::factory()->create([
                'team_id' => $user->team_id,
                'user_id' => $user->id,
            ]),
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertEquals($hostData->frontman->id, $host->frontman_id);
        $this->assertDatabaseHas('hosts', [
            'frontman_id' => $hostData->frontman->id,
        ]);
    }

    /** @test */
    public function can_set_subunit_when_creating_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'subUnit' => SubUnit::factory()->create([
                'team_id' => $user->team_id,
            ]),
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertEquals($hostData->subUnit->id, $host->sub_unit_id);
        $this->assertDatabaseHas('hosts', [
            'sub_unit_id' => $hostData->subUnit->id,
        ]);
    }

    /** @test */
    public function can_set_v2_snmp_data_when_creating_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'  => 'v2',
                'community' => 'snmp-community',
            ],
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertEquals('v2', $host->snmp_protocol);
        $this->assertEquals('snmp-community', $host->snmp_community);
        $this->assertEquals(161, $host->snmp_port);
        $this->assertEquals(5, $host->snmp_timeout);
        $this->assertNull($host->snmp_username);
        $this->assertNull($host->snmp_privacy_protocol);
        $this->assertNull($host->snmp_security_level);
        $this->assertNull($host->snmp_authentication_protocol);
        $this->assertNull($host->snmp_authentication_password);
        $this->assertNull($host->snmp_privacy_password);
    }

    /** @test */
    public function will_fail_without_community_when_creating_host_with_v2_snmp()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'  => 'v2',
                'community' => null,
            ],
        ]);

        $this->expectException(HostException::class);
        $this->expectExceptionMessage('SNMP community is required with v2.');

        resolve(CreateHostAction::class)->execute($user, $hostData);
    }

    /** @test */
    public function can_set_v3_snmp_data_when_creating_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'               => 'v3',
                'community'              => 'snmp-community',
                'port'                   => 100,
                'timeout'                => 2,
                'privacyProtocol'        => 'aes',
                'securityLevel'          => 'authPriv',
                'authenticationProtocol' => 'sha',
                'username'               => 'snmp-username',
                'authenticationPassword' => 'snmp-auth-password',
                'privacyPassword'        => 'snmp-privacy-password',
            ],
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertEquals('v3', $host->snmp_protocol);
        $this->assertEquals('snmp-community', $host->snmp_community);
        $this->assertEquals(100, $host->snmp_port);
        $this->assertEquals(2, $host->snmp_timeout);
        $this->assertEquals('snmp-username', $host->snmp_username);
        $this->assertEquals('aes', $host->snmp_privacy_protocol);
        $this->assertEquals('authPriv', $host->snmp_security_level);
        $this->assertEquals('sha', $host->snmp_authentication_protocol);
        $this->assertEquals('snmp-auth-password', $host->snmp_authentication_password);
        $this->assertEquals('snmp-privacy-password', $host->snmp_privacy_password);
    }

    /** @test */
    public function will_fail_without_security_level_when_creating_host_with_v3_snmp()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'               => 'v3',
                'community'              => 'snmp-community',
                'port'                   => 100,
                'timeout'                => 2,
                'privacyProtocol'        => 'aes',
                'securityLevel'          => null,
                'authenticationProtocol' => 'sha',
                'username'               => 'snmp-username',
                'authenticationPassword' => 'snmp-auth-password',
                'privacyPassword'        => 'snmp-privacy-password',
            ],
        ]);

        $this->expectException(HostException::class);
        $this->expectExceptionMessage('SNMP security level is required with v3.');

        resolve(CreateHostAction::class)->execute($user, $hostData);
    }

    /** @test */
    public function can_add_tags_when_creating_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'tags' => ['host-tag-1', 'host-tag-2'],
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertCount(2, $host->tags);
        $this->assertEquals(['host-tag-1', 'host-tag-2'], $host->tags->pluck('name')->all());
    }

    /** @test */
    public function will_create_disk_full_rule_when_creating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertTrue($host->cagent);
        $this->assertDatabaseHas('rules', [
            'team_id'             => $user->team_id,
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Alert(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'check_type'          => RuleCheckType::Cagent(),
            'function'            => RuleFunction::Last(),
            'operator'            => RuleOperator::LessThan(),
            'check_key'           => 'fs.free_percent.*',
            'threshold'           => 5,
            'results_range'       => 1,
            'updated_at'          => $now,
            'created_at'          => $now,
        ]);
    }

    /** @test */
    public function will_create_low_memory_rule_when_creating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertTrue($host->cagent);
        $this->assertDatabaseHas('rules', [
            'team_id'             => $user->team_id,
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Warn(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'check_type'          => RuleCheckType::Cagent(),
            'function'            => RuleFunction::Average(),
            'operator'            => RuleOperator::LessThan(),
            'check_key'           => 'mem.available_percent',
            'threshold'           => 5,
            'results_range'       => 3,
            'updated_at'          => $now,
            'created_at'          => $now,
        ]);
    }

    /** @test */
    public function will_create_cpu_high_load_rule_when_creating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertTrue($host->cagent);
        $this->assertDatabaseHas('rules', [
            'team_id'             => $user->team_id,
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Warn(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'check_type'          => RuleCheckType::Cagent(),
            'function'            => RuleFunction::Average(),
            'operator'            => RuleOperator::LessThan(),
            'check_key'           => 'cpu.util.idle.*.total',
            'threshold'           => 10,
            'results_range'       => 3,
            'updated_at'          => $now,
            'created_at'          => $now,
        ]);
    }

    /** @test */
    public function will_create_module_alert_rule_when_creating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertTrue($host->cagent);
        $this->assertDatabaseHas('rules', [
            'team_id'             => $user->team_id,
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Alert(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'check_type'          => RuleCheckType::Cagent(),
            'function'            => RuleFunction::Last(),
            'operator'            => RuleOperator::NotEmpty(),
            'check_key'           => 'modules',
            'threshold'           => 1,
            'results_range'       => 0,
            'updated_at'          => $now,
            'created_at'          => $now,
        ]);
    }

    /** @test */
    public function will_create_module_warning_rule_when_creating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(CreateHostAction::class)->execute($user, $hostData);

        $host = $host->refresh();

        $this->assertTrue($host->cagent);
        $this->assertDatabaseHas('rules', [
            'team_id'             => $user->team_id,
            'active'              => true,
            'finish'              => false,
            'action'              => RuleAction::Warn(),
            'mandatory'           => false,
            'expression_alias'    => null,
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'check_type'          => RuleCheckType::Cagent(),
            'function'            => RuleFunction::Last(),
            'operator'            => RuleOperator::NotEmpty(),
            'check_key'           => 'modules',
            'threshold'           => 1,
            'results_range'       => 0,
            'updated_at'          => $now,
            'created_at'          => $now,
        ]);
    }

    /** @test */
    public function will_update_flag_for_team_when_creating_first_host()
    {
        $user = $this->createUser();
        $hostData = HostDataFactory::make();

        resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertTrue($user->team->has_created_host);
        $this->assertTrue($user->team->wasChanged('has_created_host'));
    }

    /** @test */
    public function wont_update_flag_for_team_when_creating_subsequent_hosts()
    {
        $team = $this->createTeam([
            'has_created_host' => true,
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);
        Host::factory()->create();

        $hostData = HostDataFactory::make();

        resolve(CreateHostAction::class)->execute($user, $hostData);

        $this->assertFalse($team->wasChanged('has_created_host'));
    }

    /** @test */
    public function will_fail_if_name_already_exists_for_team()
    {
        $user = $this->createUser();
        Host::factory()->create([
            'name'    => 'name1',
            'team_id' => $user->team_id,
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name1',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the name 'name1' already exists.");

        resolve(CreateHostAction::class)->execute($user, $hostData);
    }

    /** @test */
    public function will_fail_if_connect_already_exists_for_team()
    {
        $user = $this->createUser();
        Host::factory()->create([
            'connect' => '8.8.8.8',
            'team_id' => $user->team_id,
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.8',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the connect (FQDN/IP) '8.8.8.8' already exists.");

        resolve(CreateHostAction::class)->execute($user, $hostData);
    }

    /** @test */
    public function will_fire_host_event_when_creating_host()
    {
        Event::fake([
            HostCreated::class,
        ]);

        $user = $this->createUser();
        $hostData = HostDataFactory::make();

        resolve(CreateHostAction::class)->execute($user, $hostData);

        Event::assertDispatched(HostCreated::class);
    }

    /** @test */
    public function will_fire_rule_events_when_creating_host_with_agent()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        resolve(CreateHostAction::class)->execute($user, $hostData);

        Event::assertDispatchedTimes(RuleCreated::class, 5);
    }

    /** @test */
    public function will_not_fire_rule_events_when_creating_host_without_agent()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $user = $this->createUser();
        $hostData = HostDataFactory::make([
            'cagent' => false,
        ]);

        resolve(CreateHostAction::class)->execute($user, $hostData);

        Event::assertNotDispatched(RuleCreated::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(NotifierService::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();
        });
    }
}
