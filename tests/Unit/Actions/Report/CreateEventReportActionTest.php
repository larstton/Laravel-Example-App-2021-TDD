<?php

namespace Tests\Unit\Actions\Report;

use App\Actions\Report\CreateEventReportAction;
use App\Models\Event;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\HostHistory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\AccessNonPublic;
use Tests\TestCase;

class CreateEventReportActionTest extends TestCase
{
    use WithoutEvents, AccessNonPublic;

    /** @test */
    public function will_build_report()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subDays(2);
        $to = now();

        Carbon::setTestNow(now()->subHour());

        $hostHistory = HostHistory::factory()->for($team)->for(
            $host1 = Host::factory()->for($team)->create()
        )->create([
            'name' => $host1->name,
        ]);

        $event = Event::factory()->for($team)->for(
            $host2 = Host::factory()->for($team)->create()
        )->withMergedMeta([
            'affectedHost' => [
                'name'    => $host2->name,
                'uuid'    => $host2->id,
                'connect' => $host2->connect,
            ],
        ])->create();

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertInstanceOf(Collection::class, $report);
        $this->assertCount(2, $report);
        $this->assertEquals($host1->name, $report[0]['name']);
        $this->assertNull($report[0]['issue']);
        $this->assertEquals($hostHistory->created_at->timestamp, $report[0]['created_at']);
        $this->assertEquals($host1->id, $report[0]['host_id']);

        $this->assertEquals($host2->name, $report[1]['name']);
        $this->assertEquals($event->meta['name'], $report[1]['check']);
        $this->assertEquals($event->id, $report[1]['uuid']);
        $this->assertEquals($event->meta['name'], $report[1]['issue']);
        $this->assertEquals($event->meta['severity'], $report[1]['severity']);
        $this->assertEquals(3600, $report[1]['time']);
        $this->assertEquals(2.08, $report[1]['percent']);
        $this->assertEquals($event->created_at->timestamp, $report[1]['created_at']);
        $this->assertEquals($host2->id, $report[1]['host_id']);
    }

    /** @test */
    public function can_get_report_from_cache_when_available()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subDays(2);
        $to = now();
        $filters = [
            'search' => 'xxx',
        ];

        Carbon::setTestNow($now = now()->subHour());

        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create();

        $key = md5($user->id.$from->timestamp.$to->timestamp.json_encode($filters));

        Cache::shouldReceive('tags->remember')
            ->once()
            ->withSomeOfArgs($key)
            ->andReturn(collect());

        resolve(CreateEventReportAction::class)->execute($user, $from, $to, $filters);
    }

    /** @test */
    public function event_will_override_host_history_no_issue_result()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subDays(2);
        $to = now();

        Carbon::setTestNow(now()->subHour());

        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name' => $host->name,
        ]);

        $event = Event::factory()->for($team)->for($host)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create();

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(1, $report);
        $this->assertEquals($event->meta['name'], $report[0]['issue']);
        $this->assertEquals($event->id, $report[0]['uuid']);
        $this->assertEquals($host->id, $report[0]['host_id']);
    }

    /** @test */
    public function will_get_soft_deleted_host_history_when_parent_host_is_deleted()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subDays(2);
        $to = now();

        Carbon::setTestNow(now()->subHour());

        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => $host->name,
            'deleted_at' => now()->subDay(),
        ]);

        $host->delete();

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function will_include_host_history_created_or_deleted_between_dates()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => $host->name,
            'created_at' => now()->subHours(15), // before "to" date
            'deleted_at' => now()->subHours(5), // after "from" date
        ]);

        // NOT included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => $host->name,
            'created_at' => now()->subHours(8), // after "to" date
            'deleted_at' => now()->subHours(5), // after "from" date
        ]);

        // included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => $host->name,
            'created_at' => now()->subHours(15), // before "to" date
            'deleted_at' => null,
        ]);

        // NOT included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => $host->name,
            'created_at' => now()->subHours(8), // after "to" date
            'deleted_at' => null,
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(2, $report);
    }

    /** @test */
    public function will_use_search_filter_on_host_history_name_when_supplied()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => 'include',
            'created_at' => now()->subHours(15), // before "to" date
            'deleted_at' => null,
        ]);

        // NOT included
        HostHistory::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'name'       => 'exclude',
            'created_at' => now()->subHours(15), // before "to" date
            'deleted_at' => null,
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to, [
            'search' => 'includ' // missing "e" intentionally as testing LIKE clause
        ]);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function will_use_search_filter_on_host_history_host_id_when_supplied()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        HostHistory::factory()->for($team)->for(
            $host1 = Host::factory()->for($team)->create()
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        // NOT included
        HostHistory::factory()->for($team)->for(
            $host2 = Host::factory()->for($team)->create()
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to, [
            'host' => $host1->id,
        ]);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function event_must_have_linked_host_or_frontman_to_be_included()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        Event::factory()->for($team)->for(
            Host::factory()->for($team)->create()
        )->create([
            'created_at' => now()->subHours(15),
        ]);
        Event::factory()->for($team)->for(
            Frontman::factory()->for($team)->create(),
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        // NOT included
        Event::factory()->for($team)->create([
            'host_id'    => null,
            'created_at' => now()->subHours(15),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(2, $report);
    }

    /** @test */
    public function will_include_events_created_or_resolved_between_dates()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(150), // before "from" date
            'resolved_at' => now()->subHours(50), // after "from" date
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(150), // before "from" date
            'resolved_at' => now()->subHours(120), // before "from" date
        ]);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(80), // after "from" date and before "to" date
            'resolved_at' => now()->subHours(5), // after "to" date
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(6), // after "from" date and after "to" date
            'resolved_at' => now()->subHours(5), // after "to" date
        ]);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(12), // before "to" date
            'resolved_at' => null,
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(5), // after "to" date
            'resolved_at' => null,
        ]);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->create([
            'created_at'  => now()->subHours(500), // before "from" date
            'resolved_at' => now()->subHours(80), // after "from" date and before "to" date
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(4, $report);
    }

    /** @test */
    public function will_use_search_filter_on_event_name_when_supplied()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create([
                'name' => 'include',
            ])
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create([
                'name' => 'exclude',
            ])
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to, [
            'search' => 'includ' // missing "e" intentionally as testing LIKE clause
        ]);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function will_use_search_filter_on_event_meta_when_supplied()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->withMergedMeta([
            'name' => 'this is included',
        ])->create([
            'created_at' => now()->subHours(15),
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host = Host::factory()->for($team)->create()
        )->withMergedMeta([
            'name' => 'this is excluded',
        ])->create([
            'created_at' => now()->subHours(15),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to, [
            'search' => 'include',
        ]);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function will_use_search_filter_on_event_host_id_when_supplied()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);

        // included
        Event::factory()->for($team)->for(
            $host1 = Host::factory()->for($team)->create()
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        // NOT included
        Event::factory()->for($team)->for(
            $host2 = Host::factory()->for($team)->create()
        )->create([
            'created_at' => now()->subHours(15),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to, [
            'host' => $host1->id,
        ]);

        $this->assertCount(1, $report);
    }

    /** @test */
    public function will_return_report_data_for_multiple_events_for_same_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->withMergedMeta([
            'name'         => 'Net Icmp Ping has failed 1 time',
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at' => now()->subHours(40),
        ]);
        Event::factory()->for($team)->for($host)->withMergedMeta([
            'name'         => 'Last measurement of CPU Utilization idle (Total*) < 85%',
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at' => now()->subHours(40),
        ]);
        Event::factory()->for($team)->for($host)->withMergedMeta([
            'name'         => 'Backup has failed 1 time',
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at' => now()->subHours(40),
        ]);
        Event::factory()->for($team)->for($host)->withMergedMeta([
            'name'         => 'Backup has failed 1 time',
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at' => now()->subHours(40),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $this->assertCount(3, $report);
    }

    /** @test */
    public function will_calculate_duration_and_percent_using_event_start_and_resolved_at_dates()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at'  => $createdAt = now()->subHours(40),
            'resolved_at' => $resolvedAt = now()->subHours(30),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $duration = $resolvedAt->timestamp - $createdAt->timestamp;
        $percent = round(100 * $duration / ($to->timestamp - $from->timestamp), 2);

        $this->assertCount(1, $report);
        $this->assertEquals($duration, $report[0]['time']);
        $this->assertEquals($percent, $report[0]['percent']);
    }

    /** @test */
    public function will_calculate_duration_and_percent_using_event_start_and_to_dates_when_unresolved()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at'  => $createdAt = now()->subHours(40),
            'resolved_at' => null,
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $duration = $to->timestamp - $createdAt->timestamp;
        $percent = round(100 * $duration / ($to->timestamp - $from->timestamp), 2);

        $this->assertCount(1, $report);
        $this->assertEquals($duration, $report[0]['time']);
        $this->assertEquals($percent, $report[0]['percent']);
    }

    /** @test */
    public function will_calculate_duration_and_percent_using_from_and_to_dates_when_unresolved_and_created_before_from_date(
    )
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at'  => $createdAt = now()->subHours(120),
            'resolved_at' => null,
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $duration = $to->timestamp - $from->timestamp;
        $percent = round(100 * $duration / ($to->timestamp - $from->timestamp), 2);

        $this->assertCount(1, $report);
        $this->assertEquals($duration, $report[0]['time']);
        $this->assertEquals($percent, $report[0]['percent']);
    }

    /** @test */
    public function will_calculate_duration_and_percent_using_from_and_resolved_dates_when_created_before_from_date()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at'  => $createdAt = now()->subHours(120),
            'resolved_at' => $resolvedAt = now()->subHours(50),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $duration = $resolvedAt->timestamp - $from->timestamp;
        $percent = round(100 * $duration / ($to->timestamp - $from->timestamp), 2);

        $this->assertCount(1, $report);
        $this->assertEquals($duration, $report[0]['time']);
        $this->assertEquals($percent, $report[0]['percent']);
    }

    /** @test */
    public function will_sum_duration_and_percent_when_multiple_events_for_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $from = now()->subHours(100);
        $to = now()->subHours(10);
        $host = Host::factory()->for($team)->create([
            'created_at' => now()->subHours(50),
        ]);

        Event::factory()->for($team)->for($host)->count(2)->withMergedMeta([
            'affectedHost' => [
                'name'    => $host->name,
                'uuid'    => $host->id,
                'connect' => $host->connect,
            ],
        ])->create([
            'created_at'  => $createdAt = now()->subHours(120),
            'resolved_at' => $resolvedAt = now()->subHours(50),
        ]);

        $report = resolve(CreateEventReportAction::class)->execute($user, $from, $to);

        $duration = $resolvedAt->timestamp - $from->timestamp;

        $this->assertCount(1, $report);
        $this->assertEquals($duration * 2, $report[0]['time']);
        $this->assertEquals(100, $report[0]['percent']);
    }
}
