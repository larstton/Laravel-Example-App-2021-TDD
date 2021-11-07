<?php

namespace Database\Factories;

use App\Models\Host;
use App\Models\JobmonResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobmonResultFactory extends Factory
{
    protected $model = JobmonResult::class;

    public function definition()
    {
        return [
            'host_id'    => Host::factory(),
            'job_id'     => $this->faker->name,
            'data'       => [],
            'next_run'   => now()->addHour(),
            'created_at' => now(),
        ];
    }
}
