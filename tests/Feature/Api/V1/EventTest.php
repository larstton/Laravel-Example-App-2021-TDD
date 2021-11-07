<?php

namespace Tests\Feature\Api\V1;

use App\Enums\EventAction;
use App\Enums\EventReminder;
use App\Enums\EventState;
use App\Models\ApiToken;
use App\Models\Event;
use App\Models\Team;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Tests\ApiV1TestCase;
use Tests\Concerns\WithoutTenancyChecks;

class EventTest extends ApiV1TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function must_be_authenticated_to_access_events()
    {
        $this->get('/events')
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function cannot_access_events_with_an_invalid_token()
    {
        $unsavedToken = ApiToken::factory()->make();
        $this->loginWith($unsavedToken);

        $this->get('/events')
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function can_fetch_event_list_with_expected_data()
    {
        $token = $this->login();

        /** @var Event $event */
        $event = Event::factory()->create([
            'team_id'     => $token->team_id,
            'meta'        => null,
            'resolved_at' => null,
            'action'      => EventAction::Warning,
            'state'       => EventState::Active,
            'reminders'   => EventReminder::Enabled,
        ]);

        $this->get('/events')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                'events' => [
                    [
                        'uuid'             => $event->id,
                        'checkKey'         => $event->check_key,
                        'action'           => EventAction::Warning,
                        'state'            => EventState::Active,
                        'reminders'        => (bool) EventReminder::Enabled,
                        'resolveTimestamp' => null,
                        'createTimestamp'  => $event->created_at->timestamp,
                        'meta'             => null,
                        'lastCheckValue'   => $event->last_check_value ?? 0,
                    ],
                ],
            ]);
    }

    /** @test */
    public function cannot_fetch_events_of_another_team()
    {
        /** @var ApiToken $token */
        $token = LaravelEvent::fakeFor(function () {
            return ApiToken::factory()->create([
                'team_id' => Team::factory(),
            ]);
        });

        /** @var ApiToken $anotherTeamsToken */
        $anotherTeamsToken = LaravelEvent::fakeFor(function () {
            return ApiToken::factory()->create([
                'team_id' => Team::factory(),
            ]);
        });

        /** @var Event $anotherTeamsEvent */
        $anotherTeamsEvent = LaravelEvent::fakeFor(function () use ($anotherTeamsToken) {
            return Event::factory()->create([
                'team_id' => $anotherTeamsToken->team_id,
            ]);
        });

        TenantManager::enableTenancyChecks();
        TenantManager::setCurrentTenant($token->team);
        Event::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->loginWith($token);

        $this->get('/events')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonMissing([
                'hosts' => [
                    [
                        'uuid' => $anotherTeamsEvent->id,
                    ],
                ],
            ]);
    }
}
