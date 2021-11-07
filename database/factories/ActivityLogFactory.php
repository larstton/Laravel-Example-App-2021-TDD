<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition()
    {
        return [
            'team_id'      => Team::factory(),
            'log_name'     => 'default',
            'description'  => $this->faker->sentence,
            'subject_id'   => $user = User::factory(),
            'subject_type' => User::class,
            'causer_id'    => $user,
            'causer_type'  => User::class,
            'properties'   => [],
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
