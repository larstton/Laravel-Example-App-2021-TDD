<?php

namespace Actions\User;

use App\Actions\User\ToggleSubscriptionAction;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ToggleSubscriptionActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_subscribe_to_marketing()
    {
        $team = $this->createTeam([], false);
        $user = $this->createUser([
            'team_id'      => $team->id,
            'product_news' => false,
        ], false);

        $user = resolve(ToggleSubscriptionAction::class)->execute($user);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertTrue($user->product_news);
    }

    /** @test */
    public function will_unsubscribe_from_marketing()
    {
        $team = $this->createTeam([], false);
        $user = $this->createUser([
            'team_id'      => $team->id,
            'product_news' => true,
        ], false);

        $user = resolve(ToggleSubscriptionAction::class)->execute($user);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertFalse($user->product_news);
    }

    /** @test */
    public function will_log_to_activity_log_when_subscribing()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'      => $team->id,
            'product_news' => false,
        ]);

        $user = resolve(ToggleSubscriptionAction::class)->execute($user);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => $user->id,
            'causer_type'  => User::class,
            'subject_id'   => $user->id,
            'subject_type' => User::class,
            'description'  => 'Subscribed to marketing e-mails.',
        ]);
    }

    /** @test */
    public function will_log_to_activity_log_when_unsubscribing()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'      => $team->id,
            'product_news' => true,
        ]);

        $user = resolve(ToggleSubscriptionAction::class)->execute($user);

        $this->assertDatabaseHas('activity_log', [
            'team_id'      => $team->id,
            'causer_id'    => $user->id,
            'causer_type'  => User::class,
            'subject_id'   => $user->id,
            'subject_type' => User::class,
            'description'  => 'Unsubscribed from marketing e-mails.',
        ]);
    }
}
