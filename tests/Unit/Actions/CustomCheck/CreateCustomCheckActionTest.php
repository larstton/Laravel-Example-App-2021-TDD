<?php

namespace Tests\Unit\Actions\CustomCheck;

use App\Actions\CustomCheck\CreateCustomCheckAction;
use App\Data\CustomCheck\CustomCheckData;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleOperator;
use App\Events\CustomCheck\CustomCheckCreated;
use App\Events\Rule\RuleCreated;
use App\Models\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateCustomCheckActionTest extends TestCase
{
    /** @test */
    public function will_create_custom_check_for_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        $customCheck = resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        $this->assertEquals($user->id, $customCheck->user_id);
        $this->assertEquals($host->id, $customCheck->host_id);
        $this->assertEquals('custom-check-name', $customCheck->name);
        $this->assertEquals(1000, $customCheck->expected_update_interval);
        $this->assertNotEmpty($customCheck->token);
    }

    /** @test */
    public function will_dispatch_events()
    {
        Event::fake([
            CustomCheckCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        Event::assertDispatched(CustomCheckCreated::class);
    }

    /** @test */
    public function will_create_new_smart_custom_check_rule_if_not_exists()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'       => $user->team_id,
            'action'        => RuleAction::Alert(),
            'check_type'    => RuleCheckType::CustomCheck(),
            'function'      => RuleFunction::Last(),
            'operator'      => RuleOperator::NotEmpty(),
            'check_key'     => '*alert',
            'threshold'     => 0,
            'results_range' => 1,
            'updated_at'    => $now,
            'created_at'    => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_create_new_smart_custom_check_warning_rule_if_not_exists()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'       => $user->team_id,
            'action'        => RuleAction::Warn(),
            'check_type'    => RuleCheckType::CustomCheck(),
            'function'      => RuleFunction::Last(),
            'operator'      => RuleOperator::NotEmpty(),
            'check_key'     => '*warning',
            'threshold'     => 0,
            'results_range' => 1,
            'updated_at'    => $now,
            'created_at'    => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_create_new_custom_check_success_alert_rule_if_not_exists()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'          => $user->team_id,
            'check_type'       => RuleCheckType::CustomCheck(),
            'check_key'        => '*.success',
            'expression_alias' => 'failed_1_times',
            'threshold'        => 0,
            'results_range'    => 1,
            'function'         => RuleFunction::Sum(),
            'operator'         => RuleOperator::EqualTo(),
            'updated_at'       => $now,
            'created_at'       => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_activate_custom_check_heartbeat_in_team_settings_when_creating_rule()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        team_settings($team)->set([
            'heartbeats.custom.active' => false,
        ]);

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        $this->assertTrue(team_settings($team)->get('heartbeats.custom.active')->first());
    }

    /** @test */
    public function wont_create_rules_if_they_exist()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $data = new CustomCheckData([
            'name'                   => 'custom-check-name',
            'expectedUpdateInterval' => 1000,
        ]);

        Event::fakeFor(function () use ($team) {
            tap(
                Rule::factory()->makeSmartCustomCheckAlertRule()->for($team)->make()
            )->calculateChecksum()->save();
            tap(
                Rule::factory()->makeSmartCustomCheckWarningRule()->for($team)->make()
            )->calculateChecksum()->save();
            tap(
                Rule::factory()->makeCustomCheckSuccessAlertRule()->for($team)->make()
            )->calculateChecksum()->save();
        });

        resolve(CreateCustomCheckAction::class)->execute($user, $host, $data);

        Event::assertNotDispatched(RuleCreated::class);
    }
}
