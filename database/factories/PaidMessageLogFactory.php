<?php

namespace Database\Factories;

use App\Enums\RecipientMediaType;
use App\Models\PaidMessageLog;
use App\Models\Recipient;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaidMessageLogFactory extends Factory
{
    protected $model = PaidMessageLog::class;

    public function definition()
    {
        return [
            'recipient_id' => Recipient::factory(),
            'team_id'      => Team::factory(),
            'media_type'   => RecipientMediaType::Email(),
            'sendto'       => $this->faker->email,
            'created_at'   => now(),
        ];
    }
}
