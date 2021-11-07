<?php

namespace Tests\Unit\Actions\ApiToken;

use App\Actions\ApiToken\CreateApiTokenAction;
use App\Data\ApiToken\ApiTokenData;
use App\Enums\ApiTokenCapability;
use App\Models\ApiToken;
use Tests\TestCase;

class CreateApiTokenActionTest extends TestCase
{
    /** @test */
    public function will_create_api_token_from_supplied_dto()
    {
        $this->createTeam();

        $data = new ApiTokenData([
            'name'       => 'token-name',
            'capability' => ApiTokenCapability::RW(),
        ]);

        $apiToken = resolve(CreateApiTokenAction::class)->execute($data);

        $this->assertInstanceOf(ApiToken::class, $apiToken);
        $this->assertEquals('token-name', $apiToken->name);
        $this->assertEquals(ApiTokenCapability::RW(), $apiToken->capability);
        $this->assertNotEmpty($apiToken->token);
    }
}
