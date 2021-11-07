<?php

namespace Tests\Feature\Api\V1;

use App\Enums\HostActiveState;
use App\Events\Host\HostCreated;
use App\Events\Host\HostDeleted;
use App\Events\Host\HostUpdated;
use App\Models\ApiToken;
use App\Models\Host;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\ApiV1TestCase;

class HostTest extends ApiV1TestCase
{
    /** @test */
    public function must_be_authenticated_to_access_hosts()
    {
        $this->get('/hosts')
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function must_be_authenticated_to_create_host()
    {
        $this->post('/hosts', [])
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function must_be_authenticated_to_update_host()
    {
        TenantManager::disableTenancyChecks();

        $host = Event::fakeFor(fn () => Host::factory()->create());

        $this->patch("/hosts/{$host->id}", [])
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function must_be_authenticated_to_delete_host()
    {
        TenantManager::disableTenancyChecks();

        $host = Event::fakeFor(fn () => Host::factory()->create());

        $this->delete("/hosts/{$host->id}", [])
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function cannot_access_hosts_with_an_invalid_token()
    {
        $unsavedToken = ApiToken::factory()->make();
        $this->loginWith($unsavedToken);

        $this->get('/hosts')
            ->assertSeeText('Unauthenticated')
            ->assertStatus(401);
    }

    /** @test */
    public function can_fetch_host_list_with_expected_data()
    {
        Event::fake();
        Carbon::setTestNow($now = now());
        $token = $this->login();

        $host = Host::factory()->create([
            'team_id'                => $token->team_id,
            'name'                   => $name = $this->faker->unique()->name,
            'description'            => 'I am a descriptive description',
            'connect'                => $connect = $this->faker->unique()->ipv4,
            'dashboard'              => false,
            'active'                 => HostActiveState::Active,
            'muted'                  => true,
            'cagent'                 => false,
            'cagent_last_updated_at' => now(),
            'user_id'                => $userId = Str::uuid(),
        ]);

        $this->get('/hosts')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                'hosts' => [
                    [
                        'uuid'             => $host->id,
                        'teamUuid'         => $token->team_id,
                        'name'             => $name,
                        'connect'          => $connect,
                        'dashboard'        => false,
                        'active'           => HostActiveState::Active,
                        'muted'            => true,
                        'description'      => 'I am a descriptive description',
                        'tags'             => [],
                        'cagent'           => false,
                        'cagentLastUpdate' => $now->timestamp,
                        'metrics'          => null,
                        'state'            => "PENDING",
                        'createTimestamp'  => $host->created_at->timestamp,
                        'createdByUuid'    => $userId,
                        'frontman'         => [],
                        'hub_password'     => $host->password,
                        'hub_url'          => "https://hub.cloudradar.xyz",
                    ],
                ],
            ]);
    }

    /** @test */
    public function cannot_access_hosts_that_do_not_belong_to_team_of_token()
    {
        $anotherTeamsToken = $this->login();

        /** @var Host $anotherTeamsHost */
        $anotherTeamsHost = Host::factory()->create([
            'team_id' => $anotherTeamsToken->team_id,
        ]);

        $token = $this->login();
        Host::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->get('/hosts')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonMissing([
                'hosts' => [
                    [
                        'uuid'     => $anotherTeamsHost->id,
                        'teamUuid' => $anotherTeamsToken->team_id,
                    ],
                ],
            ]);
    }

    /** @test */
    public function can_create_a_host()
    {
        $token = $this->login();

        $host = Host::factory()->make([
            'name'    => Str::random(20),
            'connect' => $this->faker->ipv4,
        ]);

        $this->post('/hosts', [
            'name'    => $host->name,
            'connect' => $host->connect,
            'team_id' => $token->team_id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('hosts', [
            'name'    => $host->name,
            'connect' => $host->connect,
            'team_id' => $token->team_id,
        ]);
    }

    /** @test */
    public function can_show_host()
    {
        $token = $this->login();

        $host = Host::factory()->create([
            'team_id'   => $token->team_id,
            'dashboard' => false,
            'active'    => true,
            'muted'     => true,
        ]);

        $this->get("/hosts/{$host->id}")
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                'host' => [
                    'uuid'      => $host->id,
                    'teamUuid'  => $token->team_id,
                    'name'      => $host->name,
                    'connect'   => $host->connect,
                    'dashboard' => false,
                    'active'    => true,
                    'muted'     => true,
                ],
            ]);
    }

    /** @test */
    public function cannot_access_host_that_does_not_belong_to_team_of_token()
    {
        $anotherTeamsToken = $this->login();
        $anotherTeamsHost = Host::factory()->create([
            'team_id' => $anotherTeamsToken->team_id,
        ]);

        $token = $this->login();

        Host::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->get("/hosts/{$anotherTeamsHost->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function can_patch_update_a_host()
    {
        $token = $this->login();

        $host = Host::factory()->create([
            'team_id'     => $token->team_id,
            'name'        => Str::random(20),
            'connect'     => $this->faker->ipv4,
            'muted'       => false,
            'dashboard'   => true,
            'active'      => true,
            'frontman_id' => '169502d5-a541-49bb-9782-4cf4d71148cf',
        ]);

        $this->patch("/hosts/{$host->id}", [
            'muted' => true,
        ])->assertStatus(200);

        $this->assertDatabaseHas('hosts', [
            'name'      => $host->name,
            'muted'     => true,
            'dashboard' => true,
            'active'    => true,
            'team_id'   => $token->team_id,
        ]);
    }

    /** @test */
    public function cannot_update_a_host_that_does_not_belong_to_team_of_token()
    {
        $anotherTeamsToken = $this->login();
        $anotherTeamsHost = Host::factory()->create([
            'team_id' => $anotherTeamsToken->team_id,
        ]);

        $token = $this->login();

        Host::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->patch("/hosts/{$anotherTeamsHost->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function can_delete_a_host()
    {
        Carbon::setTestNow($now = now());

        $token = $this->login();

        $host = Host::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->delete("/hosts/{$host->id}")->assertStatus(200);

        $this->assertDatabaseHas('hosts', [
            'name'       => $host->name,
            'team_id'    => $token->team_id,
            'deleted_at' => $now,
        ]);
    }

    /** @test */
    public function cannot_delete_a_host_that_does_not_belong_to_team_of_token()
    {
        $anotherTeamsToken = $this->login();
        $anotherTeamsHost = Host::factory()->create([
            'team_id' => $anotherTeamsToken->team_id,
        ]);

        $token = $this->login();

        $host = Host::factory()->create([
            'team_id' => $token->team_id,
        ]);

        $this->delete("/hosts/{$anotherTeamsHost->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('hosts', [
            'name'       => $host->name,
            'team_id'    => $token->team_id,
            'deleted_at' => null,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            HostCreated::class,
            HostUpdated::class,
            HostDeleted::class,
        ]);
    }
}
