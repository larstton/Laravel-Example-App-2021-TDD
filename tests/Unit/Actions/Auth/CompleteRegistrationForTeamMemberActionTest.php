<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\CompleteRegistrationForTeamMemberAction;
use App\Actions\Recipient\CreateRecipientAction;
use App\Data\Auth\TeamMemberRegisterData;
use App\Data\Recipient\RecipientData;
use App\Enums\TeamStatus;
use App\Models\Recipient;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class CompleteRegistrationForTeamMemberActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_configure_team_member_after_joining_team()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create();

        $data = new TeamMemberRegisterData([
            'nickname'        => 'nickname',
            'password'        => 'password',
            'termsAccepted'   => true,
            'privacyAccepted' => true,
        ]);

        $teamMember = resolve(CompleteRegistrationForTeamMemberAction::class)
            ->execute($teamMember, $data);

        $this->assertTrue($team->isCurrentTenant());
        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertEquals('nickname', $teamMember->nickname);
        $this->assertTrue(Hash::check('password', $teamMember->password));
        $this->assertTrue($teamMember->team_status->is(TeamStatus::Joined()));
        $this->assertTrue($teamMember->terms_accepted);
        $this->assertTrue($teamMember->privacy_accepted);
    }

    /** @test */
    public function will_create_new_recipient_from_team_member_data_if_user_settings_exists()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create();

        user_settings($teamMember)->set([
            'makeRecipient' => true,
        ]);

        $data = new TeamMemberRegisterData([
            'nickname'        => 'nickname',
            'password'        => 'password',
            'termsAccepted'   => true,
            'privacyAccepted' => true,
        ]);

        $this->mock(CreateRecipientAction::class, function (MockInterface $mock) use ($teamMember, $team) {
            $mock->shouldReceive('execute', [$teamMember], RecipientData::class)
                ->andReturn(Recipient::factory()->for($team)->create());
        });

        resolve(CompleteRegistrationForTeamMemberAction::class)
            ->execute($teamMember, $data);

        $this->assertFalse(user_settings($teamMember)->get('makeRecipient')->first());
    }
}
