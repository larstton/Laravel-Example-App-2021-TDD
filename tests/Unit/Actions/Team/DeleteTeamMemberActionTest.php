<?php

namespace Tests\Unit\Actions\Team;

use App\Actions\Team\DeleteTeamMemberAction;
use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Models\Tag;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class DeleteTeamMemberActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_team_member()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create();

        resolve(DeleteTeamMemberAction::class)->execute($teamMember);

        $this->assertDeleted($teamMember);
    }

    /** @test */
    public function will_delete_recipients_linked_to_team_member()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Email(),
            'sendto'     => $teamMember->email,
        ]);

        resolve(DeleteTeamMemberAction::class)->execute($teamMember, true);

        $this->assertDeleted($recipient);
    }

    /** @test */
    public function will_remove_team_member_id_from_tag()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'host_tag' => 'tag1',
        ]);
        $tag = Tag::factory()->for($team)->withTag('tag1')->create();
        $tag->addTeamMemberFilterToMeta($teamMember)->save();

        resolve(DeleteTeamMemberAction::class)->execute($teamMember);

        $tag->refresh();

        $this->assertEquals([], $tag->meta->team_member_filtering);
    }
}
