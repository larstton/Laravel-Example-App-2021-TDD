<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use App\Models\UserAgentData;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAgentDataFactory extends Factory
{
    protected $model = UserAgentData::class;

    public function definition()
    {
        return [
            'user_id'    => User::factory(),
            'team_id'    => Team::factory(),
            'data'       => [],
            'created_at' => now(),
        ];
    }
}
