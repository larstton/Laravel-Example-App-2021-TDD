<?php

namespace Tests\Unit\Actions\StatusPage;

use App\Actions\StatusPage\DeleteStatusPageAction;
use App\Models\StatusPage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class DeleteStatusPageActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_delete_status_page()
    {
        $team = $this->createTeam();
        $statusPage = StatusPage::factory()->for($team)->create();

        resolve(DeleteStatusPageAction::class)->execute($statusPage);

        $this->assertDeleted($statusPage);
    }
}
