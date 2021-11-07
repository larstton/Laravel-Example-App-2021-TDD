<?php /** @noinspection PhpFieldAssignmentTypeMismatchInspection */

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\GetRecipientLogFromNotifierAction;
use App\Models\Recipient;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class GetRecipientLogFromNotifierActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_ping_notifier_service()
    {
        $team = $this->createTeam();

        $recipient = Recipient::factory()->for($team)->create();

        $this->notifierService
            ->shouldReceive('getRecipientLogs', [$recipient, []])
            ->andReturnTrue();

        resolve(GetRecipientLogFromNotifierAction::class)->execute($recipient, []);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
