<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\GetHostAlertLogFromNotifierAction;
use App\Models\Host;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class GetHostAlertLogFromNotifierActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_send_request_to_notifier_for_alert_logs()
    {
        $team = $this->createTeam();

        $host = Host::factory()->create([
            'team_id' => $team->id,
        ]);

        $spy = $this->spy(NotifierService::class);

        resolve(GetHostAlertLogFromNotifierAction::class)->execute($host, []);

        $spy->shouldHaveReceived('getHostAlertLogs')->withArgs([$host, []]);
    }
}
