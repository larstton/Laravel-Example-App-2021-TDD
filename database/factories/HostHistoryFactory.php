<?php

namespace Database\Factories;

use App\Models\Host;
use App\Models\HostHistory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HostHistoryFactory extends Factory
{
    protected $model = HostHistory::class;

    public function definition()
    {
        return [
            'host_id'    => Host::factory(),
            'team_id'    => Team::factory(),
            'user_id'    => User::factory(),
            'name'       => $this->faker->name,
            'paid'       => true,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
