<?php

namespace Tests\Unit\Actions\Checkout;

use App\Actions\Checkout\FetchCheckoutDataAction;
use App\Support\CheckoutService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Mockery\MockInterface;
use Tests\TestCase;

class FetchCheckoutDataActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_make_and_return_checkout_data()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);

        $this->mock(CheckoutService::class, function (MockInterface $mock) use ($user, $team) {
            $mock->shouldReceive('getTeam', [$team])->andReturnUndefined();
            $mock->shouldReceive('getJsonResponse')->withNoArgs()
                ->andReturn([
                    'success' => true,
                ]);
            $mock->shouldReceive('makeCardUpdateUrl', [$user])
                ->andReturn('https://this-is-a-form-url.com');
            $mock->shouldReceive('makeLoginUrl', [$user])
                ->andReturn('https://this-is-a-base-url.com');
        });

        $response = resolve(FetchCheckoutDataAction::class)->execute($user, $team);

        $this->assertTrue(! isset($response['success']));
        $this->assertEquals('https://this-is-a-form-url.com', $response['formUrl']);
        $this->assertEquals('https://this-is-a-base-url.com', $response['handover']['baseUrl']);
        $this->assertEquals(['upgrade', 'change-billing-address', 'list-invoices', 'verify'],
            $response['handover']['actions']);
    }
}
