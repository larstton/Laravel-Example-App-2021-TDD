<?php

namespace Tests\Unit\Actions\Rule;

use App\Actions\Rule\FetchAgentProcessListAction;
use App\Enums\CheckType;
use App\Models\CheckResult;
use App\Models\Host;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class FetchAgentProcessListActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_produce_list_of_cmdline_processes_from_check_results()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();
        CheckResult::factory()->for($host)->withAgentData([
            'proc.list' => [
                [
                    'cmdline'              => '/sbin/init splash',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
                [
                    'cmdline'              => '/usr/sbin/kerneloops',
                    'memory_usage_percent' => 0,
                    'name'                 => 'kerneloops',
                    'parent_pid'           => 1,
                    'pid'                  => 941,
                    'rss'                  => 434176,
                    'state'                => 'sleeping',
                    'vms'                  => 58302464,
                ],
                [
                    'cmdline'              => '/usr/lib/policykit-1/polkitd --no-debug',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'polkitd',
                    'parent_pid'           => 1,
                    'pid'                  => 948,
                    'rss'                  => 10117120,
                    'state'                => 'sleeping',
                    'vms'                  => 304852992,
                ],
            ],
        ])->create([
            'check_type' => CheckType::Agent(),
        ]);

        $data = resolve(FetchAgentProcessListAction::class)->execute($team, 'cmdline');

        $this->assertEquals([
            "/sbin/init splash",
            "/usr/sbin/kerneloops",
            "/usr/lib/policykit-1/polkitd --no-debug",
        ], $data);
    }

    /** @test */
    public function will_produce_list_of_named_processes_from_check_results()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();
        CheckResult::factory()->for($host)->withAgentData([
            'proc.list' => [
                [
                    'cmdline'              => '/sbin/init splash',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
                [
                    'cmdline'              => '/usr/sbin/kerneloops',
                    'memory_usage_percent' => 0,
                    'name'                 => 'kerneloops',
                    'parent_pid'           => 1,
                    'pid'                  => 941,
                    'rss'                  => 434176,
                    'state'                => 'sleeping',
                    'vms'                  => 58302464,
                ],
                [
                    'cmdline'              => '/usr/lib/policykit-1/polkitd --no-debug',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'polkitd',
                    'parent_pid'           => 1,
                    'pid'                  => 948,
                    'rss'                  => 10117120,
                    'state'                => 'sleeping',
                    'vms'                  => 304852992,
                ],
            ],
        ])->create([
            'check_type' => CheckType::Agent(),
        ]);

        $data = resolve(FetchAgentProcessListAction::class)->execute($team, 'process');

        $this->assertEquals([
            'systemd',
            'kerneloops',
            'polkitd',
        ], $data);
    }

    /** @test */
    public function will_remove_duplicate_values_for_cmdline_type()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();
        CheckResult::factory()->for($host)->withAgentData([
            'proc.list' => [
                [
                    'cmdline'              => '/sbin/init splash',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
                [
                    'cmdline'              => '/sbin/init splash',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
            ],
        ])->create([
            'check_type' => CheckType::Agent(),
        ]);

        $data = resolve(FetchAgentProcessListAction::class)->execute($team, 'cmdline');

        $this->assertEquals([
            "/sbin/init splash",
        ], $data);
    }

    /** @test */
    public function will_remove_duplicate_values_for_process_type()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();
        CheckResult::factory()->for($host)->withAgentData([
            'proc.list' => [
                [
                    'cmdline'              => '/sbin/init splash 1',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
                [
                    'cmdline'              => '/sbin/init splash 2',
                    'memory_usage_percent' => 0.06,
                    'name'                 => 'systemd',
                    'parent_pid'           => 0,
                    'pid'                  => 1,
                    'rss'                  => 9924608,
                    'state'                => 'sleeping',
                    'vms'                  => 306786304,
                ],
            ],
        ])->create([
            'check_type' => CheckType::Agent(),
        ]);

        $data = resolve(FetchAgentProcessListAction::class)->execute($team, 'process');

        $this->assertEquals([
            "systemd",
        ], $data);
    }

    /** @test */
    public function will_use_cache()
    {
        $team = $this->createTeam();

        $host = Host::factory()->for($team)->create();
        CheckResult::factory()->for($host)->withAgentData()->create([
            'check_type' => CheckType::Agent(),
        ]);

        $key = "agent-process-list:{$team->id}:process";
        Cache::shouldReceive('remember')
            ->once()
            ->withSomeOfArgs($key)
            ->andReturn(Mockery::type('array'));

        resolve(FetchAgentProcessListAction::class)->execute($team, 'process');
    }
}
