<?php

namespace Tests\Unit\Actions\StatusPage;

use App\Actions\StatusPage\CreateStatusPageAction;
use App\Models\StatusPage;
use Database\Factories\StatusPageDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use Tests\TestCase;

class CreateStatusPageActionTest extends TestCase
{
    /** @test */
    public function can_create_new_status_page()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = StatusPageDataFactory::make([
            'title' => 'title',
            'meta'  => ['hello'],
        ]);

        $statusPage = resolve(CreateStatusPageAction::class)->execute($user, $data);

        $this->assertInstanceOf(StatusPage::class, $statusPage);
        $this->assertEquals($team->id, $statusPage->team_id);
        $this->assertEquals('title', $statusPage->title);
        $this->assertInstanceOf(SchemalessAttributes::class, $statusPage->meta);
        $this->assertEquals('hello', $statusPage->meta->first());
        $this->assertNotEmpty($statusPage->token);
    }
}
