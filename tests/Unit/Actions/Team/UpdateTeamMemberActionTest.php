<?php

namespace Actions\Team;

use App\Actions\Team\UpdateTeamMemberAction;
use App\Enums\TeamMemberRole;
use App\Models\SubUnit;
use App\Models\Tag;
use App\Models\TeamMember;
use Database\Factories\UpdateTeamMemberDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Arr;
use Tests\TestCase;

class UpdateTeamMemberActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_team_member()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'role'        => TeamMemberRole::Admin(),
            'host_tag'    => null,
            'sub_unit_id' => null,
        ]);
        $tag = Tag::factory()->for($team)->withTag('tag1')->create();
        $subUnit = SubUnit::factory()->for($team)->create();
        $data = UpdateTeamMemberDataFactory::make([
            'role'    => TeamMemberRole::Member(),
            'hostTag' => $tag->name,
            'subUnit' => $subUnit,
        ]);

        $teamMember = resolve(UpdateTeamMemberAction::class)->execute($teamMember, $data);

        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertTrue($teamMember->role->is(TeamMemberRole::Member()));
        $this->assertEquals($tag->name, $teamMember->host_tag);
        $this->assertEquals($subUnit->id, $teamMember->sub_unit_id);
    }

    /** @test */
    public function will_use_original_values_if_not_present_in_dto()
    {
        $team = $this->createTeam();
        $tag = Tag::factory()->for($team)->withTag('tag1')->create();
        $subUnit = SubUnit::factory()->for($team)->create();
        $teamMember = TeamMember::factory()->for($team)->create([
            'role'        => TeamMemberRole::Admin(),
            'host_tag'    => $tag->name,
            'sub_unit_id' => $subUnit->id,
        ]);

        $data = UpdateTeamMemberDataFactory::make([
            'role'    => null,
            'hostTag' => null,
            'subUnit' => null,
        ])->setHasData([
            'role'    => false,
            'hostTag' => false,
            'subUnit' => false,
        ]);

        $teamMember = resolve(UpdateTeamMemberAction::class)->execute($teamMember, $data);

        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertTrue($teamMember->role->is(TeamMemberRole::Admin()));
        $this->assertEquals($tag->name, $teamMember->host_tag);
        $this->assertEquals($subUnit->id, $teamMember->sub_unit_id);
    }

    /** @test */
    public function will_add_team_member_filter_to_tag_when_adding_tag_to_team_member()
    {
        $team = $this->createTeam();
        $teamMember = TeamMember::factory()->for($team)->create([
            'host_tag' => null,
        ]);
        /** @var Tag $tag */
        $tag = Tag::factory()->for($team)->withTag('tag1')->create();
        $data = UpdateTeamMemberDataFactory::make([
            'hostTag' => $tag->name,
        ]);

        $teamMember = resolve(UpdateTeamMemberAction::class)->execute($teamMember, $data);

        $tag->refresh();

        $this->assertEquals($teamMember->id, $tag->meta->team_member_filtering[0]);
    }

    /** @test */
    public function will_remove_team_member_filter_from_tag_when_removing_tag_from_team_member()
    {
        $team = $this->createTeam();
        /** @var Tag $tag */
        $tag = Tag::factory()->for($team)->withTag('tag1')->create();
        $teamMember = TeamMember::factory()->for($team)->create([
            'host_tag' => $tag->name,
        ]);
        $tag->addTeamMemberFilterToMeta($teamMember)->save();
        $data = UpdateTeamMemberDataFactory::make([
            'hostTag' => null,
        ]);

        resolve(UpdateTeamMemberAction::class)->execute($teamMember, $data);

        $tag->refresh();

        $this->assertEquals([], $tag->meta->team_member_filtering);
    }
}
