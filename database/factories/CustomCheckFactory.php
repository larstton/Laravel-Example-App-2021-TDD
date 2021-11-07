<?php

namespace Database\Factories;

use App\Models\CustomCheck;
use App\Models\Host;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomCheckFactory extends Factory
{
    protected $model = CustomCheck::class;

    public function definition()
    {
        return [
            'id'                       => Str::uuid()->toString(),
            'host_id'                  => Host::factory(),
            'user_id'                  => User::factory(),
            'name'                     => Str::random(8),
            'token'                    => Str::random(8),
            'expected_update_interval' => 60,
            'last_influx_error'        => null,
            'last_checked_at'          => null,
            'created_at'               => null,
            'updated_at'               => null,
        ];
    }
}
