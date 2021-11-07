<?php

namespace Tests\Unit\Actions\Frontman;

use App\Actions\Frontman\CreateFrontmanAction;
use App\Events\Frontman\FrontmanCreated;
use App\Exceptions\FrontmanException;
use App\Models\Frontman;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateFrontmanActionTest extends TestCase
{
    /** @test */
    public function will_create_new_frontman_for_users_team()
    {
        Carbon::setTestNow($now = now());
        Event::fake([
            FrontmanCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $frontman = resolve(CreateFrontmanAction::class)->execute($user, 'custom location');

        $this->assertInstanceOf(Frontman::class, $frontman);
        $this->assertEquals('custom location', $frontman->location);
        $this->assertNotEmpty($frontman->password);
        $this->assertEquals($user->id, $frontman->user_id);
        $this->assertEquals($team->id, $frontman->team_id);
        $this->assertTrue($now->is($frontman->created_at));
        $this->assertTrue($now->is($frontman->updated_at));

        Event::assertDispatched(FrontmanCreated::class);
    }

    /** @test */
    public function wont_create_frontman_if_team_has_reached_the_max_allowed_frontman()
    {
        Event::fake([
            FrontmanCreated::class,
        ]);

        $team = $this->createTeam([
            'max_frontmen' => 1,
        ]);
        Frontman::factory()->for($team)->create();
        $user = $this->createUser($team);

        $this->expectException(FrontmanException::class);
        $this->expectErrorMessage('Maximum of allowed 1 frontmen reached.');
        resolve(CreateFrontmanAction::class)->execute($user, 'custom location');

        Event::assertNotDispatched(FrontmanCreated::class);
    }
}
