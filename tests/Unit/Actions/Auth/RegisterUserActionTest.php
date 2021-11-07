<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Actions\Recipient\CreateRecipientAction;
use App\Actions\Team\CreateTeamAction;
use App\Data\Auth\UserRegisterData;
use App\Data\Recipient\RecipientData;
use App\Data\Team\CreateTeamData;
use App\Enums\TeamStatus;
use App\Events\Auth\NewTeamCreated;
use App\Events\Auth\NewUserRegistered;
use App\Exceptions\TeamException;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    /** @test */
    public function will_create_new_user()
    {
        Event::fake([
            NewUserRegistered::class,
            NewTeamCreated::class,
        ]);

        $data = new UserRegisterData([
            'email'            => $this->faker->email,
            'password'         => Str::random(),
            'termsAccepted'    => true,
            'privacyAccepted'  => true,
            'trialEnd'         => null,
            'lang'             => 'en',
            'partner'          => null,
            'partnerExtraData' => null,
        ]);

        $team = Team::factory()->create();
        $this->mock(CreateTeamAction::class, function (MockInterface $mock) use ($data, $team) {
            $mock->shouldReceive('execute', [
                CreateTeamData::fromUserRegisterData($data),
            ])->andReturn($team);
        });
        $createRecipientAction = $this->spy(CreateRecipientAction::class);
        $authFactory = $this->spy(Factory::class);

        $user = resolve(RegisterUserAction::class)->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($team->isCurrentTenant());
        $this->assertEquals($team->id, $user->team_id);
        $this->assertEquals($data->email, $user->email);
        $this->assertEquals(TeamStatus::Joined(), $user->team_status->value);

        $authFactory->shouldHaveReceived('setUser', [$user]);
        $createRecipientAction->shouldHaveReceived('execute', [$user, RecipientData::class]);

        Event::assertDispatched(NewUserRegistered::class);
        Event::assertDispatched(NewTeamCreated::class);
    }

    /** @test */
    public function will_fail_if_user_has_banned_email()
    {
        config([
            'banned.emails' => [
                '*@banned-email.com',
            ],
        ]);

        $data = new UserRegisterData([
            'email'            => 'hello@banned-email.com',
            'password'         => Str::random(),
            'termsAccepted'    => true,
            'privacyAccepted'  => true,
            'trialEnd'         => null,
            'lang'             => 'en',
            'partner'          => null,
            'partnerExtraData' => null,
        ]);

        $this->expectException(TeamException::class);
        $this->expectErrorMessage("E-mail 'hello@banned-email.com' is not allowed.");

        resolve(RegisterUserAction::class)->execute($data);
    }
}
