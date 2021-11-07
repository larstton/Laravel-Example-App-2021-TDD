<?php

namespace Tests\Feature\Api\V1;

use Tests\ApiV1TestCase;
use Tests\Concerns\WithoutTenancyChecks;

class PingTest extends ApiV1TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function get_ping_should_pong()
    {
        $this->login();

        $this->get('/ping')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'error'   => null,
                'details' => 'pong',
            ]);
    }

    /** @test */
    public function post_ping_should_pong()
    {
        $this->login();

        $this->post('/ping')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'error'   => null,
                'details' => 'pong',
            ]);
    }
}
