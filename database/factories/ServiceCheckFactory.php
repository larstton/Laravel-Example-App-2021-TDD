<?php

namespace Database\Factories;

use App\Enums\CheckLastSuccess;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceCheckFactory extends Factory
{
    protected $model = ServiceCheck::class;

    public function definition()
    {
        return [
            'id'              => Str::uuid()->toString(),
            'host_id'         => Host::factory(),
            'user_id'         => User::factory(),
            'active'          => true,
            'check_interval'  => 60,
            'protocol'        => 'tcp',
            'service'         => 'https',
            'port'            => 80,
            'in_progress'     => false,
            'last_success'    => CheckLastSuccess::Pending(),
            'last_message'    => null,
            'last_checked_at' => null,
            'created_at'      => null,
            'updated_at'      => null,
        ];
    }
}
