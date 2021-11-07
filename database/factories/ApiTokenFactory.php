<?php

namespace Database\Factories;

use App\Enums\ApiTokenCapability;
use App\Models\ApiToken;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition()
    {
        return [
            'id'                    => Str::uuid()->toString(),
            'team_id'               => Team::factory(),
            'token'                 => Str::random(rand(9, 12)),
            'name'                  => $this->faker->name,
            'capability'            => ApiTokenCapability::RW,
            'last_usage_ip_address' => null,
            'last_used_at'          => now(),
            'created_at'            => now(),
            'updated_at'            => now(),
        ];
    }
}
