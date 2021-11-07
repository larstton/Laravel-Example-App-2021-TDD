<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\CreateTeamMemberAction;
use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Events\Team\TeamMemberInvited;
use App\Models\TeamMember;
use Database\Factories\CreateTeamMemberDataFactory;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateTeamMemberActionTest extends TestCase
{
    /** @test */
    public function user_can_invite_another_team_member()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'email'           => $email = $this->faker->email,
            'role'            => TeamMemberRole::Admin(),
            'createRecipient' => false,
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertEquals($email, $teamMember->email);
        $this->assertFalse($teamMember->terms_accepted);
        $this->assertFalse($teamMember->privacy_accepted);
        $this->assertFalse($teamMember->product_news);
        $this->assertTrue($teamMember->role->is(TeamMemberRole::Admin()));
        $this->assertTrue($teamMember->team_status->is(TeamStatus::Invited()));
        $this->assertEquals('en', $teamMember->lang);
        $this->assertNotEmpty($teamMember->password);
    }

    /** @test */
    public function will_dispatch_events()
    {
        Event::fake([
            TeamMemberInvited::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make();

        resolve(CreateTeamMemberAction::class)->execute($user, $data);

        Event::assertDispatched(TeamMemberInvited::class);
    }

    /** @test */
    public function will_add_user_setting_when_creating_recipient_and_not_support()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'createRecipient' => true,
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $teamMember->id,
            'value'   => $this->castToJson([
                'user-settings' => ['makeRecipient' => true],
            ]),
        ]);
    }

    /** @test */
    public function wont_add_user_setting_when_creating_recipient_and_is_support()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'email'           => 'support+100@cloudradar.co',
            'createRecipient' => true,
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $teamMember->id,
            'value'   => $this->castToJson([
                'user-settings' => ['makeRecipient' => true],
            ]),
        ]);
    }

    /** @test */
    public function will_extend_trial_for_support_when_less_than_5_days_remaining()
    {
        $team = $this->createTeam([
            'trial_ends_at' => $trialEndsAt = now()->addDays(2),
        ]);
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'email' => 'support+100@cloudradar.co',
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $teamMember->refresh();

        $this->assertDateTimesMatch($teamMember->team->trial_ends_at, $trialEndsAt->addDays(5));
    }

    /** @test */
    public function wont_extend_trial_for_support_when_not_on_trial()
    {
        $team = $this->createTeam([
            'trial_ends_at' => null,
        ]);
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'email' => 'support+100@cloudradar.co',
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $this->assertNull($teamMember->refresh()->team->trial_ends_at);
    }

    /** @test */
    public function wont_extend_trial_for_support_when_more_than_5_days_remaining()
    {
        $team = $this->createTeam([
            'trial_ends_at' => $trialEndsAt = now()->addDays(10),
        ]);
        $user = $this->createUser($team);
        $data = CreateTeamMemberDataFactory::make([
            'email' => 'support+100@cloudradar.co',
        ]);

        $teamMember = resolve(CreateTeamMemberAction::class)->execute($user, $data);

        $teamMember->refresh();

        $this->assertDateTimesMatch($teamMember->team->trial_ends_at, $trialEndsAt);
    }
}
