<?php

namespace Database\Factories;

use App\Enums\TeamPlan;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition()
    {
        return [
            'id'                            => Str::uuid()->toString(),
            'name'                          => $this->faker->name,
            'max_hosts'                     => 999,
            'max_members'                   => 99,
            'max_recipients'                => 10,
            'default_frontman_id'           => '24995c49-45ba-43d6-9205-4f5e83d32a11',
            'max_frontmen'                  => 99,
            'min_check_interval'            => 60,
            'data_retention'                => 30,
            'plan'                          => TeamPlan::Trial,
            'previous_plan'                 => null,
            'timezone'                      => 'Etc/GMT',
            'currency'                      => null,
            'registration_track'            => null,
            'partner'                       => null,
            'partner_extra_data'            => null,
            'date_format'                   => 'L.',
            'has_granted_access_to_support' => null,
            'plan_last_changed_at'          => null,
            'trial_ends_at'                 => now()->addDays(15),
            'upgraded_at'                   => null,
            'deleted_at'                    => null,
            'created_at'                    => now(),
            'updated_at'                    => now(),
        ];
    }
}
