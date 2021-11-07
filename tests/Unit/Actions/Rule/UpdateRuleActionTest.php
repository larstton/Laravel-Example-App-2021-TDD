<?php

namespace Tests\Unit\Actions\Rule;

use App\Actions\Rule\UpdateRuleAction;
use App\Enums\EventState;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Events\Rule\RuleUpdated;
use App\Models\Rule;
use App\Support\NotifierService;
use Database\Factories\RuleDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateRuleActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function can_update_existing_rule()
    {
        $team = $this->createTeam();

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create();
        tap($rule)->calculateChecksum()->save();

        $data = RuleDataFactory::make([
            'hostMatchPart'     => RuleHostMatchPart::UUID(),
            'hostMatchCriteria' => 'any',
            'checkType'         => [
                RuleCheckType::Cagent(),
            ],
            'checkKey'          => '*.failure',
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => RuleFunction::Last(),
            'operator'          => RuleOperator::GreaterThan(),
            'resultsRange'      => 50,
            'threshold'         => 50.0,
            'unit'              => RuleThresholdUnit::Minute(),
            'active'            => false,
            'action'            => RuleAction::Warn(),
            'finish'            => true,
            'expressionAlias'   => null,
        ]);

        resolve(UpdateRuleAction::class)->execute($rule, $data);

        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertTrue($rule->host_match_part->is(RuleHostMatchPart::UUID()));
        $this->assertEquals('any', $rule->host_match_criteria);
        $this->assertTrue($rule->check_type[0]->is(RuleCheckType::Cagent()));
        $this->assertEquals('*.failure', $rule->check_key);
        $this->assertEquals(['key' => '', 'value' => ''], $rule->key_function);
        $this->assertTrue($rule->function->is(RuleFunction::Last()));
        $this->assertTrue($rule->operator->is(RuleOperator::GreaterThan()));
        $this->assertEquals(50, $rule->results_range);
        $this->assertEquals(50.0, $rule->threshold);
        $this->assertTrue($rule->unit->is(RuleThresholdUnit::Minute()));
        $this->assertFalse($rule->active);
        $this->assertTrue($rule->action->is(RuleAction::Warn()));
        $this->assertTrue($rule->finish);
        $this->assertNull($rule->expression_alias);
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            RuleUpdated::class,
        ]);

        $team = $this->createTeam();
        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create();
        tap($rule)->calculateChecksum()->save();

        $data = RuleDataFactory::make();

        resolve(UpdateRuleAction::class)->execute($rule, $data);

        Event::assertDispatched(RuleUpdated::class);
    }

    /** @test */
    public function will_throw_validation_exception_if_rule_already_exists_for_team()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'host_match_part' => RuleHostMatchPart::Name(),
        ]);
        tap($rule)->calculateChecksum()->save();

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'host_match_part' => RuleHostMatchPart::UUID(),
        ]);
        tap($rule)->calculateChecksum()->save();

        $data = RuleDataFactory::make([
            'hostMatchPart'     => RuleHostMatchPart::Name(),
            'hostMatchCriteria' => $rule->host_match_criteria,
            'checkType'         => $rule->check_type,
            'checkKey'          => $rule->check_key,
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => $rule->function,
            'operator'          => $rule->operator,
            'resultsRange'      => $rule->results_range,
            'threshold'         => $rule->threshold,
            'unit'              => $rule->unit,
            'active'            => $rule->active,
            'action'            => $rule->action,
            'finish'            => $rule->finish,
            'expressionAlias'   => $rule->expression_alias,
        ]);

        $this->expectException(ValidationException::class);

        resolve(UpdateRuleAction::class)->execute($rule, $data);
    }

    /** @test */
    public function will_throw_validation_exception_if_rule_unit_changed_from_null_to_byte_and_clashes()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'unit' => null,
        ]);
        tap($rule)->calculateChecksum()->save();

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'unit' => RuleThresholdUnit::MegaByte(),
        ]);
        tap($rule)->calculateChecksum()->save();

        $data = RuleDataFactory::make([
            'hostMatchPart'     => $rule->host_match_part,
            'hostMatchCriteria' => $rule->host_match_criteria,
            'checkType'         => $rule->check_type,
            'checkKey'          => $rule->check_key,
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => $rule->function,
            'operator'          => $rule->operator,
            'resultsRange'      => $rule->results_range,
            'threshold'         => $rule->threshold,
            'unit'              => RuleThresholdUnit::Byte(),
            'active'            => $rule->active,
            'action'            => $rule->action,
            'finish'            => $rule->finish,
            'expressionAlias'   => $rule->expression_alias,
        ]);

        $this->expectException(ValidationException::class);

        resolve(UpdateRuleAction::class)->execute($rule, $data);
    }

    /** @test */
    public function will_throw_validation_exception_if_rule_unit_changed_from_null_to_second_and_clashes()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'unit' => null,
        ]);
        tap($rule)->calculateChecksum()->save();

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->for($user)->create([
            'unit' => RuleThresholdUnit::Minute(),
        ]);
        tap($rule)->calculateChecksum()->save();

        $data = RuleDataFactory::make([
            'hostMatchPart'     => $rule->host_match_part,
            'hostMatchCriteria' => $rule->host_match_criteria,
            'checkType'         => $rule->check_type,
            'checkKey'          => $rule->check_key,
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => $rule->function,
            'operator'          => $rule->operator,
            'resultsRange'      => $rule->results_range,
            'threshold'         => $rule->threshold,
            'unit'              => RuleThresholdUnit::Second(),
            'active'            => $rule->active,
            'action'            => $rule->action,
            'finish'            => $rule->finish,
            'expressionAlias'   => $rule->expression_alias,
        ]);

        $this->expectException(ValidationException::class);

        resolve(UpdateRuleAction::class)->execute($rule, $data);
    }

    /** @test */
    public function will_update_linked_event_action_if_action_changed_on_rule()
    {
        $team = $this->createTeam();
        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create([
            'action' => RuleAction::Alert(),
        ]);
        tap($rule)->calculateChecksum()->save();

        $events = \App\Models\Event::factory()->for($team)->for($rule)->count(2)->create([
            'state'  => EventState::Active(),
            'action' => RuleAction::Alert,
        ]);

        $data = RuleDataFactory::make([
            'action' => RuleAction::Warn(),
        ]);

        resolve(UpdateRuleAction::class)->execute($rule, $data);

        $events->each(function (\App\Models\Event $event) {
            $event->refresh();
            $this->assertTrue($event->action->is(RuleAction::Warn));
        });
    }

    /** @test */
    public function will_delete_event_and_ping_notifier_service_if_set_attributes_updated()
    {
        $team = $this->createTeam();
        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create([
            'function' => RuleFunction::Last(),
        ]);
        tap($rule)->calculateChecksum()->save();

        $events = \App\Models\Event::factory()->for($team)->for($rule)->count(2)->create([
            'state' => EventState::Active(),
        ]);

        $data = RuleDataFactory::make([
            'function' => RuleFunction::Average(),
        ]);

        resolve(UpdateRuleAction::class)->execute($rule, $data);

        $events->each(function (\App\Models\Event $event) {
            $this->notifierService->shouldReceive('recoverEvent', $event)
                ->andReturnTrue();
            $this->assertDeleted($event);
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
