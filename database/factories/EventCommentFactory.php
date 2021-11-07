<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventComment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventCommentFactory extends Factory
{
    protected $model = EventComment::class;

    public function definition()
    {
        return [
            'id'                => Str::uuid()->toString(),
            'team_id'           => Team::factory(),
            'user_id'           => User::factory(),
            'event_id'          => Event::factory(),
            'nickname'          => null,
            'text'              => $this->faker->sentence(),
            'visible_to_guests' => false,
            'forward'           => false,
            'created_at'        => now(),
        ];
    }
}
