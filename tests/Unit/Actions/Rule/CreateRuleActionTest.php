<?php

namespace Tests\Unit\Actions\Rule;

use App\Actions\Rule\CreateRuleAction;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Events\Rule\RuleCreated;
use App\Models\Rule;
use Database\Factories\RuleDataFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateRuleActionTest extends TestCase
{
    /** @test */
    public function will_create_new_rule()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = RuleDataFactory::make([
            'hostMatchPart'     => RuleHostMatchPart::None(),
            'hostMatchCriteria' => 'any',
            'checkType'         => [
                RuleCheckType::ServiceCheck(),
            ],
            'checkKey'          => '*.success',
            'keyFunction'       => ['key' => '', 'value' => ''],
            'function'          => RuleFunction::Average(),
            'operator'          => RuleOperator::LessThan(),
            'resultsRange'      => 5,
            'threshold'         => 10.0,
            'unit'              => RuleThresholdUnit::GigaByte(),
            'active'            => true,
            'action'            => RuleAction::Alert(),
            'finish'            => false,
            'expressionAlias'   => null,
        ]);

        $rule = resolve(CreateRuleAction::class)->execute($user, $data);

        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertTrue($rule->host_match_part->is(RuleHostMatchPart::None()));
        $this->assertEquals('any', $rule->host_match_criteria);
        $this->assertTrue($rule->check_type[0]->is(RuleCheckType::ServiceCheck()));
        $this->assertEquals('*.success', $rule->check_key);
        $this->assertEquals(['key' => '', 'value' => ''], $rule->key_function);
        $this->assertTrue($rule->function->is(RuleFunction::Average()));
        $this->assertTrue($rule->operator->is(RuleOperator::LessThan()));
        $this->assertEquals(5, $rule->results_range);
        $this->assertEquals(10.0, $rule->threshold);
        $this->assertTrue($rule->unit->is(RuleThresholdUnit::GigaByte()));
        $this->assertTrue($rule->active);
        $this->assertTrue($rule->action->is(RuleAction::Alert()));
        $this->assertFalse($rule->finish);
        $this->assertNull($rule->expression_alias);
    }

    /** @test */
    public function will_dispatch_created_event()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = RuleDataFactory::make();

        resolve(CreateRuleAction::class)->execute($user, $data);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function will_throw_validation_exception_if_rule_already_exists_for_team()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create();
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
            'unit'              => $rule->unit,
            'active'            => $rule->active,
            'action'            => $rule->action,
            'finish'            => $rule->finish,
            'expressionAlias'   => $rule->expression_alias,
        ]);

        $this->expectException(ValidationException::class);

        resolve(CreateRuleAction::class)->execute($user, $data);
    }

    /** @test */
    public function will_exclude_unit_if_byte_when_calculating_check_sum()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create([
            'unit' => null,
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

        resolve(CreateRuleAction::class)->execute($user, $data);
    }

    /** @test */
    public function will_exclude_unit_if_second_when_calculating_check_sum()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        /** @var Rule $rule */
        $rule = Rule::factory()->for($team)->create([
            'unit' => null,
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

        resolve(CreateRuleAction::class)->execute($user, $data);
    }
}
