<?php

namespace Tests\Unit\Actions\StatusPage;

use App\Actions\StatusPage\UpdateStatusPageAction;
use App\Models\StatusPage;
use Database\Factories\StatusPageDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UpdateStatusPageActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function can_update_status_page()
    {
        $team = $this->createTeam();

        $statusPage = StatusPage::factory()->for($team)->create([
            'title' => 'old-title',
            'meta'  => [],
        ]);

        $data = StatusPageDataFactory::make([
            'title' => 'new-title',
            'meta'  => ['hello'],
        ]);

        $statusPage = resolve(UpdateStatusPageAction::class)->execute($statusPage, $data);

        $this->assertInstanceOf(StatusPage::class, $statusPage);
        $this->assertEquals('new-title', $statusPage->title);
        $this->assertEquals('hello', $statusPage->meta->first());
    }
}
