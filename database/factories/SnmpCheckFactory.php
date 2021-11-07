<?php

namespace Database\Factories;

use App\Enums\CheckLastSuccess;
use App\Models\Host;
use App\Models\SnmpCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SnmpCheckFactory extends Factory
{
    protected $model = SnmpCheck::class;

    public function definition()
    {
        return [
            'id'              => Str::uuid()->toString(),
            'host_id'         => Host::factory(),
            'user_id'         => User::factory(),
            'active'          => true,
            'check_interval'  => 60,
            'preset'          => 'bandwidth',
            'last_success'    => CheckLastSuccess::Pending(),
            'last_message'    => null,
            'last_checked_at' => null,
            'created_at'      => null,
            'updated_at'      => null,
        ];
    }
}
