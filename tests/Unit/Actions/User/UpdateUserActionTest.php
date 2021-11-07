<?php

namespace Actions\User;

use App\Actions\User\UpdateUserAction;
use App\Models\User;
use Database\Factories\UpdateUserDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_user()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'nickname',
            'name'     => 'name',
            'lang'     => 'de',
        ]);
        $data = UpdateUserDataFactory::make([
            'nickname' => 'new-nickname',
            'name'     => 'new-name',
            'lang'     => 'en',
        ]);

        $user = resolve(UpdateUserAction::class)->execute($user, $data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('new-nickname', $user->nickname);
        $this->assertEquals('new-name', $user->name);
        $this->assertEquals('en', $user->lang);
    }

    /** @test */
    public function can_null_values()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'nickname',
            'name'     => 'name',
            'lang'     => 'de',
        ]);
        $data = UpdateUserDataFactory::make([
            'nickname' => null,
            'name'     => null,
            'lang'     => null,
        ]);

        $user = resolve(UpdateUserAction::class)->execute($user, $data);

        $this->assertNull($user->nickname);
        $this->assertNull($user->name);
        $this->assertEquals('de', $user->lang);
    }

    /** @test */
    public function will_use_original_values_if_not_present_in_dto()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id'  => $team->id,
            'nickname' => 'nickname',
            'name'     => 'name',
            'lang'     => 'de',
        ]);
        $data = UpdateUserDataFactory::make([
            'nickname' => null,
            'name'     => null,
            'lang'     => null,
        ])->setHasData([
            'nickname' => false,
            'name'     => false,
            'lang'     => false,
        ]);

        $user = resolve(UpdateUserAction::class)->execute($user, $data);

        $this->assertEquals('nickname', $user->nickname);
        $this->assertEquals('name', $user->name);
        $this->assertEquals('de', $user->lang);
    }
}
