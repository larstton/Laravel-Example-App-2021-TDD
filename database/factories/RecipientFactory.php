<?php

namespace Database\Factories;

use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipientFactory extends Factory
{
    protected $model = Recipient::class;

    public function definition()
    {
        return [
            'id'                           => Str::uuid()->toString(),
            'team_id'                      => Team::factory(),
            'user_id'                      => User::factory(),
            'verified'                     => true,
            'active'                       => true,
            'verification_token'           => null,
            'permanent_failures_last_24_h' => false,
            'administratively_disabled'    => false,
            'media_type'                   => RecipientMediaType::Email(),
            'sendto'                       => $this->faker->unique()->email,
            'description'                  => $this->faker->sentence(),
            'option1'                      => null,
            'reminder_delay'               => 600,
            'maximum_reminders'            => 3,
            'reminders'                    => false,
            'daily_reports'                => false,
            'monthly_reports'              => false,
            'daily_summary'                => false,
            'weekly_reports'               => false,
            'comments'                     => false,
            'alerts'                       => false,
            'warnings'                     => false,
            'event_uuids'                  => false,
            'recoveries'                   => false,
            'rules'                        => null,
            'extra_data'                   => null,
            'verified_at'                  => null,
            'created_at'                   => now(),
            'updated_at'                   => now(),
        ];
    }

    public function subscribedToComments()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified' => true,
                'active'   => true,
                'comments' => true,
            ];
        });
    }
}
