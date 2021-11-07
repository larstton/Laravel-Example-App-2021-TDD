<?php

namespace Tests;

use App\Models\ApiToken;
use App\Models\Host;
use App\Models\Team;
use App\Models\User;
use App\Support\Tenancy\Facades\TenantManager;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Neves\Testing\RefreshDatabase;
use Tests\Concerns\AttachJwtToken;
use Tests\Concerns\DateHelpers;
use Tests\Concerns\MultiDatabaseMigrations;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker, AttachJwtToken, MultiDatabaseMigrations, RefreshDatabase, DateHelpers;

    public $seed = true;

    public function json($method, $uri, array $data = [], array $headers = [])
    {
        return parent::json($method, env('ENGINE_URL').$uri, $data, $headers);
    }

    public function createTeam($attributes = [], $setTeamAsCurrentTenant = true): Team
    {
        return tap(
            Event::fakeFor(fn () => Team::factory()->create($attributes)),
            function (Team $team) use ($setTeamAsCurrentTenant) {
                if ($setTeamAsCurrentTenant) {
                    TenantManager::setCurrentTenant($team);
                }
            }
        );
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function createHost($attributes = []): Host
    {
        return Event::fakeFor(fn () => Host::factory()->create(
            $this->normaliseAttributes($attributes)
        ));
    }

    private function normaliseAttributes($attributes)
    {
        if (is_a($attributes, Team::class)) {
            $attributes = [
                'team_id' => $attributes->id,
            ];
        }

        return $attributes;
    }

    public function createUser($attributes = [], $setTeamAsCurrentTenant = true): User
    {
        return tap(Event::fakeFor(fn () => User::factory()->create(
            $this->normaliseAttributes($attributes)
        )), function (User $user) use ($setTeamAsCurrentTenant) {
            if ($setTeamAsCurrentTenant) {
                TenantManager::setCurrentTenant($user->team);
            }
        });
    }

    public function createApiToken($attributes = [], $setTeamAsCurrentTenant = true): ApiToken
    {
        return tap(Event::fakeFor(fn () => ApiToken::factory()->create(
            $this->normaliseAttributes($attributes)
        )), function (ApiToken $apiToken) use ($setTeamAsCurrentTenant) {
            if ($setTeamAsCurrentTenant) {
                TenantManager::setCurrentTenant($apiToken->team);
            }
        });
    }

    protected function castToJson($json)
    {
        if (is_array($json)) {
            $json = addslashes(json_encode($json));
        } elseif (is_null($json) || is_null(json_decode($json))) {
            throw new Exception('A valid JSON string was not provided.');
        }

        return DB::raw("CAST('{$json}' AS JSON)");
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (method_exists($this, 'disableTenancyChecksForAllTests')) {
            $this->disableTenancyChecksForAllTests();
        }
    }
}
