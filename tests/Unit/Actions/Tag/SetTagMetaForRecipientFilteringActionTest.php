<?php

namespace Tests\Unit\Actions\Tag;

use App\Actions\Tag\SetTagMetaForRecipientFilteringAction;
use App\Models\Recipient;
use App\Models\Tag;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SetTagMetaForRecipientFilteringActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_add_meta_on_tag_for_existing_recipient_rule_filtering()
    {
        $team = $this->createTeam();

        $tag1 = Tag::factory()->for($team)->withTag('tag1')->create();
        $tag2 = Tag::factory()->for($team)->withTag('tag2')->create();

        $recipient = Recipient::factory()->for($team)->create([
            'rules' => [
                'data' => [
                    [
                        'field' => 'tags',
                        'value' => ['tag1', 'tag2'],
                    ],
                ],
            ],
        ]);

        resolve(SetTagMetaForRecipientFilteringAction::class)->execute($recipient);

        $tag1->refresh();
        $tag2->refresh();

        $this->assertEquals($recipient->id, $tag1->meta->recipient_filtering[0]);
        $this->assertEquals($recipient->id, $tag2->meta->recipient_filtering[0]);
    }

    /** @test */
    public function will_remove_meta_on_tag_for_deleted_recipients()
    {
        $team = $this->createTeam();

        $tag1 = Tag::factory()->for($team)->withTag('tag1')->create();
        $tag2 = Tag::factory()->for($team)->withTag('tag2')->create();

        $recipient = Recipient::factory()->for($team)->create([
            'rules' => [
                'data' => [
                    [
                        'field' => 'tags',
                        'value' => ['tag1', 'tag2'],
                    ],
                ],
            ],
        ]);

        $tag1->addRecipientFilterToMeta($recipient)->save();
        $tag2->addRecipientFilterToMeta($recipient)->save();
        $recipient->delete();

        resolve(SetTagMetaForRecipientFilteringAction::class)->execute($recipient);

        $tag1->refresh();
        $tag2->refresh();

        $this->assertEquals([], $tag1->meta->recipient_filtering);
        $this->assertEquals([], $tag2->meta->recipient_filtering);
    }

    /** @test */
    public function will_sync_recipient_to_tag_when_updating_existing()
    {
        $team = $this->createTeam();

        $tag1 = Tag::factory()->for($team)->withTag('tag1')->create();
        $tag2 = Tag::factory()->for($team)->withTag('tag2')->create();

        $recipient = Recipient::factory()->for($team)->create([
            'rules' => [
                'data' => [
                    [
                        'field' => 'tags',
                        'value' => ['tag1'],
                    ],
                ],
            ],
        ]);

        $tag1->addRecipientFilterToMeta($recipient)->save();

        $recipient->rules = [
            'data' => [
                [
                    'field' => 'tags',
                    'value' => ['tag2'],
                ],
            ],
        ];

        resolve(SetTagMetaForRecipientFilteringAction::class)->execute($recipient);

        $tag1->refresh();
        $tag2->refresh();

        $this->assertEquals([], $tag1->meta->recipient_filtering);
        $this->assertEquals($recipient->id, $tag2->meta->recipient_filtering[0]);
    }
}
