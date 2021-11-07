<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\ApiToken\CreateApiTokenAction;
use App\Actions\Auth\ConfigureNewPleskTeamPostVerificationAction;
use App\Data\ApiToken\ApiTokenData;
use App\Models\ApiToken;
use App\Models\Frontman;
use Illuminate\Foundation\Testing\WithoutEvents;
use Mockery\MockInterface;
use Tests\TestCase;

class ConfigureNewPleskTeamPostVerificationActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_configure_teams_default_frontman()
    {
        $team = $this->createTeam([
            'default_frontman_id' => Frontman::DEFAULT_FRONTMAN_UUID,
        ], false);

        $this->mock(CreateApiTokenAction::class)->shouldIgnoreMissing();

        resolve(ConfigureNewPleskTeamPostVerificationAction::class)->execute($team);

        $this->assertEquals(config('cloudradar.frontman.base_frontmen.eu_west'), $team->default_frontman_id);
    }

    /** @test */
    public function will_create_and_return_new_api_token_if_not_exists()
    {
        $team = $this->createTeam([], false);

        $this->mock(CreateApiTokenAction::class, function (MockInterface $mock) use ($team) {
            $mock->shouldReceive('execute', [ApiTokenData::class])
                ->andReturn(ApiToken::factory()
                    ->for($team)
                    ->create([
                        'name' => 'Plesk server 2',
                    ]));
        });

        $apiToken = resolve(ConfigureNewPleskTeamPostVerificationAction::class)->execute($team);

        $this->assertInstanceOf(ApiToken::class, $apiToken);
        $this->assertEquals('Plesk server 2', $apiToken->name);
    }

    /** @test */
    public function will_return_existing_api_token_if_exists()
    {
        $team = $this->createTeam([], false);

        $existingToken = ApiToken::factory()
            ->for($team)
            ->create([
                'name' => 'Plesk server',
            ]);

        $apiToken = resolve(ConfigureNewPleskTeamPostVerificationAction::class)->execute($team);

        $this->assertInstanceOf(ApiToken::class, $apiToken);
        $this->assertEquals('Plesk server', $apiToken->name);
        $this->assertEquals($existingToken->id, $apiToken->id);
    }
}
