<?php

namespace Database\Factories;

use App\Models\Frontman;
use App\Models\Host;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HostFactory extends Factory
{
    protected $model = Host::class;

    public function definition()
    {
        return [
            'id'                            => Str::uuid()->toString(),
            'name'                          => $this->faker->name,
            'team_id'                       => $team = Team::factory(),
            'frontman_id'                   => Frontman::factory()->create(),
            'sub_unit_id'                   => null,
            'description'                   => $this->faker->paragraph,
            'state'                         => 'PENDING',
            'user_id'                       => Str::uuid()->toString(),
            'last_update_user_id'           => null,
            'password'                      => Str::random(12),
            'connect'                       => $this->faker->ipv4,
            'active'                        => true,
            'cagent'                        => false,
            'cagent_last_updated_at'        => now()->subHour(),
            'snmp_check_last_updated_at'    => now()->subHour(),
            'web_check_last_updated_at'     => now()->subHour(),
            'service_check_last_updated_at' => now()->subHour(),
            'custom_check_last_updated_at'  => now()->subHour(),
            'inventory'                     => [],
            'cagent_metrics'                => null,
            'dashboard'                     => true,
            'muted'                         => false,
            'hw_inventory'                  => [],
            'snmp_protocol'                 => null,
            'snmp_port'                     => null,
            'snmp_community'                => null,
            'snmp_timeout'                  => null,
            'snmp_privacy_protocol'         => null,
            'snmp_security_level'           => null,
            'snmp_authentication_protocol'  => null,
            'snmp_username'                 => null,
            'snmp_authentication_password'  => null,
            'snmp_privacy_password'         => null,
            'deleted_at'                    => null,
            'created_at'                    => now(),
            'updated_at'                    => now(),
        ];
    }
}
