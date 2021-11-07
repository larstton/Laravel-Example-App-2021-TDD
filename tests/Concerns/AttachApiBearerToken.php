<?php

namespace Tests\Concerns;

use App\Models\ApiToken;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Support\Facades\Event;

trait AttachApiBearerToken
{
    protected ?ApiToken $loginApiToken = null;

    public function login(): ApiToken
    {
        return tap(Event::fakeFor(fn () => ApiToken::factory()->create()), function ($token) {
            $this->loginWith($token);
        });
    }

    public function loginWith(ApiToken $apiToken): self
    {
        $this->loginApiToken = $apiToken;
        TenantManager::enableTenancyChecks();
        TenantManager::setCurrentTenant($apiToken->team);

        return $this;
    }

    protected function requestNeedsToken(): bool
    {
        return ! is_null($this->loginApiToken);
    }

    protected function attachToken(array $server): array
    {
        return array_merge($server, $this->transformHeadersToServerVars([
            'Authorization' => 'Bearer '.$this->getApiToken(),
        ]));
    }

    protected function getApiToken()
    {
        return $this->loginApiToken->token;
    }
}
