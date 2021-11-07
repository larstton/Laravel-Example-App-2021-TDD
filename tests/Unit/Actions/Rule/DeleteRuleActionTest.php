<?php

namespace Tests\Unit\Actions\Rule;

use App\Actions\Rule\DeleteRuleAction;
use App\Events\Rule\RuleDeleted;
use App\Models\Rule;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DeleteRuleActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_rule()
    {
        $team = $this->createTeam();

        $rule = Rule::factory()->for($team)->create();

        resolve(DeleteRuleAction::class)->execute($rule);

        $this->assertDeleted($rule);
    }

    /** @test */
    public function will_dispatch_deleted_event()
    {
        Event::fake([
            RuleDeleted::class,
        ]);

        $team = $this->createTeam();

        $rule = Rule::factory()->for($team)->create();

        resolve(DeleteRuleAction::class)->execute($rule);

        Event::assertDispatched(RuleDeleted::class);
    }

    /** @test */
    public function will_delete_events_linked_to_rule()
    {
        $team = $this->createTeam();

        $rule = Rule::factory()->for($team)->create();
        $events = \App\Models\Event::factory()->for($team)->for($rule)->count(2)->create();

        resolve(DeleteRuleAction::class)->execute($rule);

        $this->assertDeleted($events[0]);
        $this->assertDeleted($events[1]);
    }
}
