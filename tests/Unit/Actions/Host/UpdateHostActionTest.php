<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\UpdateHostAction;
use App\Enums\EventState;
use App\Enums\HostActiveState;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Events\Event\EventDeleted;
use App\Events\Event\EventUpdated;
use App\Events\Host\HostUpdated;
use App\Events\Rule\RuleCreated;
use App\Events\Rule\RuleDeleted;
use App\Exceptions\HostException;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\Rule;
use App\Models\SubUnit;
use App\Models\Tag;
use App\Support\NotifierService;
use Database\Factories\HostDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateHostActionTest extends TestCase
{
    /** @test */
    public function can_update_existing_host()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
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

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertInstanceOf(Host::class, $host);
        $this->assertEquals('host-name', $host->name);
        $this->assertEquals($user->team_id, $host->team_id);
        $this->assertEquals($user->team->default_frontman_id, $host->frontman_id);
        $this->assertNull($host->sub_unit_id);
        $this->assertEquals('host-description', $host->description);
        $this->assertTrue($host->active->is(HostActiveState::Active()));
        $this->assertEquals((string) $user->id, (string) $host->user_id);
        $this->assertEquals((string) $user->id, $host->last_update_user_id);
        $this->assertNotEmpty($host->password);
        $this->assertEquals('8.8.8.8', $host->connect);
        $this->assertFalse($host->cagent);
        $this->assertEmpty($host->inventory);
        $this->assertEquals(0, $host->cagent_metrics);
        $this->assertTrue($host->dashboard);
        $this->assertFalse($host->muted);
        $this->assertEmpty($host->hw_inventory);
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
    public function can_update_host_with_user_as_authed_entity()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make();

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertEquals($hostData->name, $host->name);
        $this->assertEquals($hostData->connect, $host->connect);
        $this->assertDatabaseHas('hosts', [
            'name'    => $hostData->name,
            'connect' => $hostData->connect,
        ]);
    }

    /** @test */
    public function can_update_host_with_api_token_as_authed_entity()
    {
        $apiToken = $this->createApiToken();
        $host = Host::factory()->create([
            'team_id' => $apiToken->team_id,
            'user_id' => $apiToken->id,
        ]);
        $hostData = HostDataFactory::make();

        $host = resolve(UpdateHostAction::class)->execute($apiToken, $host, $hostData);

        $this->assertEquals($hostData->name, $host->name);
        $this->assertEquals($hostData->connect, $host->connect);
        $this->assertDatabaseHas('hosts', [
            'name'    => $hostData->name,
            'connect' => $hostData->connect,
        ]);
    }

    /** @test */
    public function can_set_frontman_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make([
            'frontman' => Frontman::factory()->create([
                'team_id' => $user->team_id,
                'user_id' => $user->id,
            ]),
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertEquals($hostData->frontman->id, $host->frontman_id);
        $this->assertDatabaseHas('hosts', [
            'frontman_id' => $hostData->frontman->id,
        ]);
    }

    /** @test */
    public function will_reset_frontman_to_team_default_if_no_frontman_supplied()
    {
        $team = $this->createTeam([
            'default_frontman_id' => '24995c49-45ba-43d6-9205-4f5e83d32a11',
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
        ], false);
        $host = Host::factory()->hasFrontman([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ])->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $hostData = HostDataFactory::make([
            'frontman' => null,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertEquals($team->default_frontman_id, $host->frontman_id);
        $this->assertDatabaseHas('hosts', [
            'frontman_id' => $team->default_frontman_id,
        ]);
    }

    /** @test */
    public function can_set_subunit_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make([
            'subUnit' => SubUnit::factory()->create([
                'team_id' => $user->team_id,
            ]),
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertEquals($hostData->subUnit->id, $host->sub_unit_id);
        $this->assertDatabaseHas('hosts', [
            'sub_unit_id' => $hostData->subUnit->id,
        ]);
    }

    /** @test */
    public function can_null_subunit_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()
            ->hasSubUnit(['team_id' => $user->team_id])
            ->create([
                'team_id' => $user->team_id,
                'user_id' => $user->id,
            ]);
        $hostData = HostDataFactory::make([
            'subUnit' => null,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host->refresh();

        $this->assertNull($host->sub_unit_id);
    }

    /** @test */
    public function can_set_v2_snmp_data_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id'                      => $user->team_id,
            'user_id'                      => $user->id,
            'snmp_protocol'                => null,
            'snmp_port'                    => null,
            'snmp_community'               => null,
            'snmp_timeout'                 => null,
            'snmp_privacy_protocol'        => null,
            'snmp_security_level'          => null,
            'snmp_authentication_protocol' => null,
            'snmp_username'                => null,
            'snmp_authentication_password' => null,
            'snmp_privacy_password'        => null,
        ]);
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'  => 'v2',
                'community' => 'snmp-community',
            ],
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_fail_without_community_when_updating_host_with_v2_snmp()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make([
            'snmpData' => [
                'protocol'  => 'v2',
                'community' => null,
            ],
        ]);

        $this->expectException(HostException::class);
        $this->expectExceptionMessage('SNMP community is required with v2.');

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);
    }

    /** @test */
    public function can_set_v3_snmp_data_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id'                      => $user->team_id,
            'user_id'                      => $user->id,
            'snmp_protocol'                => null,
            'snmp_port'                    => null,
            'snmp_community'               => null,
            'snmp_timeout'                 => null,
            'snmp_privacy_protocol'        => null,
            'snmp_security_level'          => null,
            'snmp_authentication_protocol' => null,
            'snmp_username'                => null,
            'snmp_authentication_password' => null,
            'snmp_privacy_password'        => null,
        ]);
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

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_fail_without_security_level_when_updating_host_with_v3_snmp()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
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

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);
    }

    /** @test */
    public function will_remove_all_snmp_data_if_updating_host_with_missing_snmp_data()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id'        => $user->team_id,
            'user_id'        => $user->id,
            'snmp_protocol'  => 'v2',
            'snmp_community' => 'snmp-community',
        ]);
        $hostData = HostDataFactory::make([
            'snmpData' => [],
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host = $host->refresh();

        $this->assertNull($host->snmp_protocol);
        $this->assertNull($host->snmp_community);
        $this->assertNull($host->snmp_port);
        $this->assertNull($host->snmp_timeout);
        $this->assertNull($host->snmp_username);
        $this->assertNull($host->snmp_privacy_protocol);
        $this->assertNull($host->snmp_security_level);
        $this->assertNull($host->snmp_authentication_protocol);
        $this->assertNull($host->snmp_authentication_password);
        $this->assertNull($host->snmp_privacy_password);
    }

    /** @test */
    public function can_add_tags_when_updating_host()
    {
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);

        $this->assertEmpty($host->tags);

        $hostData = HostDataFactory::make([
            'tags' => ['host-tag-1', 'host-tag-2'],
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host = $host->refresh();

        $this->assertCount(2, $host->tags);
        $this->assertEquals(['host-tag-1', 'host-tag-2'], $host->tags->pluck('name')->all());
    }

    /** @test */
    public function will_sync_tags_when_tags_supplied_to_host_with_existing_tags()
    {
        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->hasAttached(
            Tag::findOrCreate(['host-tag-1'], 'host')
        )->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);

        $this->assertNotEmpty($host->tags);

        $hostData = HostDataFactory::make([
            'tags' => ['host-tag-2', 'host-tag-3'],
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host = $host->refresh();

        $this->assertCount(2, $host->tags);
        $this->assertEquals(['host-tag-2', 'host-tag-3'], $host->tags->pluck('name')->all());
    }

    /** @test */
    public function will_remove_tags_when_updating_host_with_no_tags()
    {
        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->hasAttached(
            Tag::findOrCreate(['host-tag-1'], 'host')
        )->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);

        $this->assertNotEmpty($host->tags);

        $hostData = HostDataFactory::make([
            'tags' => [null],
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host = $host->refresh();

        $this->assertEmpty($host->tags);
    }

    /** @test */
    public function will_create_disk_full_rule_when_updating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => false,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_create_low_memory_rule_when_updating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => false,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_create_cpu_high_load_rule_when_updating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => false,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_create_module_alert_rule_when_updating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => false,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_create_module_warning_rule_when_updating_first_host_with_agent()
    {
        Carbon::setTestNow($now = now());

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => false,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

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
    public function will_fail_if_name_already_exists_for_team()
    {
        $user = $this->createUser();
        Host::factory()->create([
            'name'    => 'name1',
            'team_id' => $user->team_id,
        ]);
        $host = Host::factory()->create([
            'name'    => 'name2',
            'team_id' => $user->team_id,
        ]);

        $hostData = HostDataFactory::make([
            'name' => 'name1',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the name 'name1' already exists.");

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);
    }

    /** @test */
    public function will_fail_if_connect_already_exists_for_team()
    {
        $user = $this->createUser();
        Host::factory()->create([
            'connect' => '8.8.8.8',
            'team_id' => $user->team_id,
        ]);
        $host = Host::factory()->create([
            'connect' => '8.8.8.1',
            'team_id' => $user->team_id,
        ]);

        $hostData = HostDataFactory::make([
            'connect' => '8.8.8.8',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("A host with the connect (FQDN/IP) '8.8.8.8' already exists.");

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);
    }

    /** @test */
    public function will_fire_host_event_when_updating_host()
    {
        Event::fake([
            HostUpdated::class,
        ]);

        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make();

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        Event::assertDispatched(HostUpdated::class);
    }

    /** @test */
    public function will_fire_rule_events_when_updating_host_with_agent()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => true,
        ]);

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        Event::assertDispatchedTimes(RuleCreated::class, 5);
    }

    /** @test */
    public function will_not_fire_rule_events_when_updating_host_without_agent()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ]);
        $hostData = HostDataFactory::make([
            'cagent' => false,
        ]);

        resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        Event::assertNotDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_remove_agent_rules_if_host_updated_without_agent_and_it_is_the_last_one()
    {
        Event::fake([RuleDeleted::class]);

        $user = $this->createUser();

        $ownerIds = [
            'team_id' => $user->team_id,
            'user_id' => $user->id,
        ];

        $rule1 = Rule::factory()->makeDiskFullAlertRule()->create($ownerIds);
        $rule2 = Rule::factory()->makeLowMemoryWarningRule()->create($ownerIds);
        $rule3 = Rule::factory()->makeCPUHighLoadWarningRule()->create($ownerIds);
        $rule4 = Rule::factory()->makeModuleAlertRule()->create($ownerIds);
        $rule5 = Rule::factory()->makeModuleWarningRule()->create($ownerIds);

        /** @var Host $host */
        $host = Host::factory()
            ->create([
                'team_id' => $user->team_id,
                'user_id' => $user->id,
                'cagent'  => true,
            ]);
        $hostData = HostDataFactory::make([
            'cagent' => false,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $host = $host->refresh();

        $this->assertFalse($host->cagent);
        $this->assertDeleted($rule1);
        $this->assertDeleted($rule2);
        $this->assertDeleted($rule3);
        $this->assertDeleted($rule4);
        $this->assertDeleted($rule5);

        Event::assertDispatchedTimes(RuleDeleted::class, 5);
    }

    /** @test */
    public function will_remove_stale_events_when_removing_agent_from_host()
    {
        Event::fake([
            EventDeleted::class,
        ]);
        $user = $this->createUser();
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'cagent'  => true,
        ]);

        \App\Models\Event::factory()->count(3)->create([
            'team_id'  => $user->team_id,
            'host_id'  => $host->id,
            'check_id' => $host->id,
        ]);

        $hostData = HostDataFactory::make([
            'cagent' => false,
        ]);

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertCount(0, $host->events);
        $this->assertDatabaseMissing('events', [
            'host_id'  => $host->id,
            'check_id' => $host->id,
        ]);

        Event::assertDispatchedTimes(EventDeleted::class, 3);
    }

    /** @test */
    public function will_recover_and_delete_host_events_when_muting_host()
    {
        Carbon::setTestNow($now = now());
        Event::fake([
            EventUpdated::class,
        ]);

        $user = $this->createUser();
        /** @var Host $host */
        $host = Host::factory()->create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'muted'   => false,
            'cagent'  => false,
        ]);

        \App\Models\Event::factory()->create([
            'team_id'     => $user->team_id,
            'host_id'     => $host->id,
            'check_id'    => $host->id,
            'resolved_at' => null,
        ]);

        $hostData = HostDataFactory::make([
            'muted'  => true,
            'cagent' => false,
        ]);

        $this->mock(NotifierService::class, function (MockInterface $mock) use ($host) {
            $mock->shouldReceive('updateHost', [$host])->andReturnTrue();
            $mock->shouldReceive('recoverEvent', [$host->events->first()])->andReturnTrue();
        });

        $host = resolve(UpdateHostAction::class)->execute($user, $host, $hostData);

        $this->assertEquals(EventState::Recovered(), $host->events->first()->state);
        $this->assertDateTimesMatch($now, $host->events->first()->resolved_at);

        Event::assertDispatched(EventUpdated::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(NotifierService::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();
        });
    }
}
