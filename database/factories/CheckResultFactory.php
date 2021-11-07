<?php

namespace Database\Factories;

use App\Enums\CheckType;
use App\Models\CheckResult;
use App\Models\Host;
use App\Models\WebCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckResultFactory extends Factory
{
    protected $model = CheckResult::class;

    public function definition()
    {
        return [
            'check_id'        => WebCheck::factory(),
            'host_id'         => Host::factory(),
            'check_type'      => CheckType::WebCheck(),
            'data'            => [],
            'success'         => null,
            'frontman_id'     => null,
            'user_agent'      => null,
            'data_updated_at' => null,
            'created_at'      => now(),
        ];
    }

    public function withAgentData(array $measurementsData = [])
    {
        return $this->state(function (array $attributes) use ($measurementsData) {
            $data = [
                'cagent.success'          => 1,
                'cpu.load.avg.1'          => 0.61,
                'cpu.util.idle.1.cpu0'    => null,
                'cpu.util.idle.1.cpu1'    => null,
                'cpu.util.idle.1.total'   => null,
                'cpu.util.iowait.1.cpu0'  => null,
                'cpu.util.iowait.1.cpu1'  => null,
                'cpu.util.iowait.1.total' => null,
                'cpu.util.system.1.cpu0'  => null,
                'cpu.util.system.1.cpu1'  => null,
                'cpu.util.system.1.total' => null,
                'cpu.util.user.1.cpu0'    => null,
                'cpu.util.user.1.cpu1'    => null,
                'cpu.util.user.1.total'   => null,
                'fs.free_B./'             => 21271617536,
                'fs.free_B./home'         => 15291240448,
                'fs.free_percent./'       => 70.55,
                'fs.free_percent./home'   => 82.51,
                'fs.total_B./'            => 31787560960,
                'fs.total_B./home'        => 19549782016,
                'listeningports.list'     => [
                    0  => [
                        'addr'  => '127.0.0.53:53',
                        'proto' => 'tcp',
                    ],
                    1  => [
                        'addr'  => '127.0.0.1:631',
                        'proto' => 'tcp',
                    ],
                    2  => [
                        'addr'  => ':::80',
                        'proto' => 'tcp6',
                    ],
                    3  => [
                        'addr'  => '::1:631',
                        'proto' => 'tcp6',
                    ],
                    4  => [
                        'addr'  => '0.0.0.0:36788',
                        'proto' => 'udp',
                    ],
                    5  => [
                        'addr'  => '0.0.0.0:5353',
                        'proto' => 'udp',
                    ],
                    6  => [
                        'addr'  => '127.0.0.53:53',
                        'proto' => 'udp',
                    ],
                    7  => [
                        'addr'  => '0.0.0.0:68',
                        'proto' => 'udp',
                    ],
                    8  => [
                        'addr'  => '0.0.0.0:631',
                        'proto' => 'udp',
                    ],
                    9  => [
                        'addr'  => ':::35221',
                        'proto' => 'udp6',
                    ],
                    10 => [
                        'addr'    => ':::5353',
                        'pid'     => 2594,
                        'program' => 'chrome',
                        'proto'   => 'udp6',
                    ],
                ],
                'local_timestamp'         => 1608196934,
                'mem.available_B'         => 12014702592,
                'mem.available_percent'   => 72,
                'mem.buff_B'              => 166690816,
                'mem.buff_percent'        => 1,
                'mem.cached_B'            => 3801513984,
                'mem.cached_percent'      => 23,
                'mem.free_B'              => 9465831424,
                'mem.free_percent'        => 57,
                'mem.shared_B'            => 1070968832,
                'mem.shared_percent'      => 6,
                'mem.total_B'             => 16605130752,
                'mem.used_B'              => 3171094528,
                'mem.used_percent'        => 19,
                'net.in_B_per_s.enp3s0'   => null,
                'net.out_B_per_s.enp3s0'  => null,
                'proc.list'               => [
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
                        'cmdline'              => '',
                        'memory_usage_percent' => 0,
                        'name'                 => 'migration/0',
                        'parent_pid'           => 2,
                        'pid'                  => 10,
                        'rss'                  => 0,
                        'state'                => 'sleeping',
                        'vms'                  => 0,
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
                'proc.possible_states'    => [
                    'blocked',
                    'zombie',
                    'stopped',
                    'running',
                    'sleeping',
                    'dead',
                    'paging',
                    'idle',
                ],
                'services.list'           => [
                    [
                        'active_state' => 'active',
                        'description'  => 'Accounts Service',
                        'load_state'   => 'loaded',
                        'manager'      => 'systemd',
                        'name'         => 'accounts-daemon.service',
                        'state'        => 'running',
                    ],
                    [
                        'active_state' => 'active',
                        'description'  => 'Run anacron jobs',
                        'load_state'   => 'loaded',
                        'manager'      => 'systemd',
                        'name'         => 'anacron.service',
                        'state'        => 'running',
                    ],
                ],
                'system.cpu_model'        => 'Intel(R) Core(TM) i7-6700HQ CPU @ 2.60GHz',
                'system.fqdn'             => 'rafal',
                'system.ipv4.1'           => '192.168.0.107',
                'system.ipv6.1'           => 'fe80::6a3f:3bb5:2fd8:7cc',
                'system.memory_total_B'   => 16605130752,
                'system.os_arch'          => 'amd64',
                'system.os_family'        => 'debian',
                'system.os_kernel'        => 'linux',
                'system.uname'            => 'Linux rafal 4.13.0-36-generic #40~16.04.1-Ubuntu SMP Fri Feb 16 23:25:58 UTC 2018 x86_64 x86_64 x86_64 GNU/Linux
',
                'temperatures.list'       => [
                    [
                        'critical_threshold' => 99,
                        'sensor_name'        => 'acpitz::temp1',
                        'temperature'        => 40,
                        'unit'               => 'centigrade',
                    ],
                    [
                        'critical_threshold' => 85,
                        'sensor_name'        => 'acpitz::temp2',
                        'temperature'        => 86,
                        'unit'               => 'centigrade',
                    ],
                    [
                        'critical_threshold' => 0,
                        'sensor_name'        => 'pch_skylake::temp1',
                        'temperature'        => 57.5,
                        'unit'               => 'centigrade',
                    ],
                ],
            ];

            return [
                'data' => [
                    'measurements' => array_replace($data, $measurementsData),
                ],
            ];
        });
    }
}
