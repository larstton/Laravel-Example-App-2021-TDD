<?php

namespace Tests\Unit\Actions\Frontman;

use App\Actions\Frontman\UpdateFrontmanAction;
use App\Events\Frontman\FrontmanUpdated;
use App\Models\Frontman;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateFrontmanActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_update_frontman()
    {
        $frontman = Frontman::factory()->create([
            'location' => 'location1',
        ]);

        resolve(UpdateFrontmanAction::class)->execute($frontman, 'location2');

        $this->assertEquals('location2', $frontman->location);
        Event::assertDispatched(FrontmanUpdated::class);
    }
}
