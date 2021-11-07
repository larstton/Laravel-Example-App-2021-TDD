<?php

namespace Actions\User;

use App\Actions\Team\DeleteTeamAction;
use App\Actions\User\DeleteUserAction;
use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Support\CheckoutService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_user_if_not_team_admin()
    {
        $team = $this->createTeam();
        $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);
        $user = $this->createUser([
            'team_id'     => $team->id,
            'role'        => TeamMemberRole::Member(),
            'email'       => $this->faker->email,
            'team_status' => TeamStatus::Joined(),
            'nickname'    => 'nickname',
            'notes'       => 'notes',
        ]);

        resolve(DeleteUserAction::class)->execute($user);

        $this->assertEquals($user->id.'@DELETED', $user->email);
        $this->assertTrue($user->team_status->is(TeamStatus::Deleted()));
        $this->assertTrue($user->role->is(TeamMemberRole::Deleted()));
        $this->assertNull($user->nickname);
        $this->assertNull($user->notes);
    }

    /** @test */
    public function will_delete_user_settings_if_not_team_admin()
    {
        $team = $this->createTeam();
        $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Member(),
        ]);
        config([
            'settings-user-settings.test-value' => true,
        ]);
        user_settings($user)->set([
            'test-value' => false,
        ]);

        resolve(DeleteUserAction::class)->execute($user);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function will_delete_user_if_not_the_only_team_admin()
    {
        $team = $this->createTeam();
        $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);
        $user = $this->createUser([
            'team_id'     => $team->id,
            'role'        => TeamMemberRole::Admin(),
            'email'       => $this->faker->email,
            'team_status' => TeamStatus::Joined(),
        ]);

        resolve(DeleteUserAction::class)->execute($user);

        $this->assertEquals($user->id.'@DELETED', $user->email);
        $this->assertTrue($user->team_status->is(TeamStatus::Deleted()));
        $this->assertTrue($user->role->is(TeamMemberRole::Deleted()));
    }

    /** @test */
    public function will_delete_user_settings_if_not_the_only_team_admin()
    {
        $team = $this->createTeam();
        $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);
        $user = $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);
        config([
            'settings-user-settings.test-value' => true,
        ]);
        user_settings($user)->set([
            'test-value' => false,
        ]);

        resolve(DeleteUserAction::class)->execute($user);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function will_throw_exception_if_unpaid_invoices_when_deleting_last_admin_user()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);

        $this->mock(CheckoutService::class, function (MockInterface $mock) use ($team) {
            $mock->shouldReceive('teamHasUnpaidInvoices', [$team])
                ->andReturnTrue();
        });

        $this->expectException(ValidationException::class);

        resolve(DeleteUserAction::class)->execute($user);
    }

    /** @test */
    public function will_delete_team_if_no_unpaid_invoices_when_deleting_last_admin_user()
    {
        $team = $this->createTeam();
        $user = $this->createUser([
            'team_id' => $team->id,
            'role'    => TeamMemberRole::Admin(),
        ]);

        $this->mock(CheckoutService::class, function (MockInterface $mock) use ($team) {
            $mock->shouldReceive('teamHasUnpaidInvoices', [$team])
                ->andReturnFalse();
        });
        $this->mock(DeleteTeamAction::class, function (MockInterface $mock) use ($team) {
            $mock->shouldReceive('execute', [$team]);
        });

        resolve(DeleteUserAction::class)->execute($user);
    }
}
