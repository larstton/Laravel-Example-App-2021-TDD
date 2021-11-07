<?php

namespace Actions\Team;

use App\Actions\Team\VerifyTeamMemberInviteAction;
use App\Models\TeamMember;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerifyTeamMemberInviteActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_verify_team_member_email()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'email_verified_at' => null,
        ]);
        $token = sha1($teamMember->email);

        Carbon::setTestNow($now = now());

        $teamMember = resolve(VerifyTeamMemberInviteAction::class)->execute($teamMember, $token);

        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertTrue($teamMember->team->isCurrentTenant());
        $this->assertDateTimesMatch($teamMember->email_verified_at, $now);
    }

    /** @test */
    public function wont_verify_team_member_email_if_already_verified()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'email_verified_at' => now()->subDay(),
        ]);
        $token = sha1($teamMember->email);

        Carbon::setTestNow($now = now());

        $teamMember = resolve(VerifyTeamMemberInviteAction::class)->execute($teamMember, $token);

        $this->assertDateTimesDoNotMatch($teamMember->email_verified_at, $now);
    }

    /** @test */
    public function will_throw_exception_if_tokens_dont_match()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'email_verified_at' => now()->subDay(),
        ]);
        $token = sha1(Str::random(8));

        Carbon::setTestNow($now = now());

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');

        resolve(VerifyTeamMemberInviteAction::class)->execute($teamMember, $token);
    }
}
