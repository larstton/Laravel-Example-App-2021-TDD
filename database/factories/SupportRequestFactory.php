<?php

namespace Database\Factories;

use App\Enums\SupportRequestState;
use App\Models\SupportRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupportRequestFactory extends Factory
{
    protected $model = SupportRequest::class;

    public function definition()
    {
        return [
            'id'         => Str::uuid()->toString(),
            'user_id'    => User::factory(),
            'team_id'    => Team::factory(),
            'email'      => $this->faker->unique()->email,
            'subject'    => $this->faker->sentence(),
            'body'       => $this->faker->sentence(),
            'state'      => SupportRequestState::Open(),
            'attachment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
