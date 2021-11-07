<?php

namespace Actions\Team;

use App\Actions\Team\HandleMovingToFrozenPlanAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Events\Event\EventUpdated;
use App\Models\Event;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\Rule;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class HandleMovingToFrozenPlanActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_disable_reminders_for_active_team_events_linked_to_rules()
    {
        $team = $this->createTeam();

        $events = Event::factory()->for($team)->count(2)->create([
            'rule_id'   => Rule::factory()->for($team),
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        $events->each(function (Event $event) {
            $event->refresh();
            $this->assertTrue($event->reminders->is(EventReminder::Disabled()));
        });
    }

    /** @test */
    public function wont_disable_reminders_for_non_active_team_events()
    {
        $team = $this->createTeam();

        $events = Event::factory()->for($team)->count(2)->create([
            'rule_id'   => Rule::factory()->for($team),
            'state'     => EventState::Recovered(),
            'reminders' => EventReminder::Enabled(),
        ]);

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        $events->each(function (Event $event) {
            $event->refresh();
            $this->assertTrue($event->reminders->is(EventReminder::Enabled()));
        });
    }

    /** @test */
    public function wont_disable_reminders_for_if_rule_missing()
    {
        $team = $this->createTeam();

        $events = Event::factory()->for($team)->count(2)->create([
            'rule_id'   => $rule = Rule::factory()->for($team)->create(),
            'state'     => EventState::Recovered(),
            'reminders' => EventReminder::Enabled(),
        ]);
        $rule->delete();

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        $events->each(function (Event $event) {
            $event->refresh();
            $this->assertTrue($event->reminders->is(EventReminder::Enabled()));
        });
    }

    /** @test */
    public function will_disable_reminders_for_active_team_events_where_check_id_is_rule_id_and_check_id_is_host()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();

        $events = Event::factory()->for($team)->count(2)->create([
            'check_id'  => $host->id,
            'rule_id'   => $host->id,
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        $events->each(function (Event $event) {
            $event->refresh();
            $this->assertTrue($event->reminders->is(EventReminder::Disabled()));
        });
    }

    /** @test */
    public function will_disable_reminders_for_active_team_events_where_check_id_is_rule_id_and_check_id_is_frontman()
    {
        $team = $this->createTeam();

        $frontman = Frontman::factory()->for($team)->create();

        $events = Event::factory()->for($team)->count(2)->create([
            'check_id'  => $frontman->id,
            'rule_id'   => $frontman->id,
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        $events->each(function (Event $event) {
            $event->refresh();
            $this->assertTrue($event->reminders->is(EventReminder::Disabled()));
        });
    }

    /** @test */
    public function will_dispatch_updated_events()
    {
        \Illuminate\Support\Facades\Event::fake([
            EventUpdated::class,
        ]);
        $team = $this->createTeam();

        Event::factory()->for($team)->count(2)->create([
            'rule_id'   => Rule::factory()->for($team),
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);
        $frontman = Frontman::factory()->for($team)->create();
        Event::factory()->for($team)->count(2)->create([
            'check_id'  => $frontman->id,
            'rule_id'   => $frontman->id,
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);
        $host = Host::factory()->for($team)->create();
        Event::factory()->for($team)->count(2)->create([
            'check_id'  => $host->id,
            'rule_id'   => $host->id,
            'state'     => EventState::Active(),
            'reminders' => EventReminder::Enabled(),
        ]);
        Event::factory()->for($team)->count(2)->create([
            'rule_id'   => Rule::factory()->for($team),
            'state'     => EventState::Recovered(),
            'reminders' => EventReminder::Enabled(),
        ]);
        Event::factory()->for($team)->count(2)->create([
            'rule_id'   => $rule = Rule::factory()->for($team)->create(),
            'state'     => EventState::Recovered(),
            'reminders' => EventReminder::Enabled(),
        ]);
        $rule->delete();

        resolve(HandleMovingToFrozenPlanAction::class)->execute($team);

        \Illuminate\Support\Facades\Event::assertDispatchedTimes(EventUpdated::class, 6);
    }
}
