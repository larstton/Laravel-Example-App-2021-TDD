<?php

namespace Database\Factories;

use App\Models\Frontman;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FrontmanFactory extends Factory
{
    protected $model = Frontman::class;

    public function definition()
    {
        return [
            'id'                        => Str::uuid()->toString(),
            'team_id'                   => value($team = Team::factory()->create())->id,
            'location'                  => $this->faker->name,
            'last_heartbeat_at'         => null,
            'password'                  => Str::random(12),
            'user_id'                   => User::factory([
                'team_id' => $team->id,
            ]),
            'host_info'                 => null,
            'host_info_last_updated_at' => null,
            'version'                   => null,
            'created_at'                => now(),
            'updated_at'                => now(),
        ];
    }
}
