<?php

namespace Tests\Unit\Actions\Support;

use App\Actions\Support\UpdateSupportRequestAction;
use App\Enums\SupportRequestState;
use App\Models\SupportRequest;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateSupportRequestActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_support_request()
    {
        $team = $this->createTeam();
        $supportRequest = SupportRequest::factory()->for($team)->create([
            'state' => SupportRequestState::Open(),
        ]);

        $supportRequest = resolve(UpdateSupportRequestAction::class)->execute(
            $supportRequest,
            SupportRequestState::Closed()
        );

        $this->assertInstanceOf(SupportRequest::class, $supportRequest);
        $this->assertTrue($supportRequest->state->is(SupportRequestState::Closed()));
    }
}
