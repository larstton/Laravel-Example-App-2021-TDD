<?php

namespace Database\Factories;

use App\Models\StatusPage;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StatusPageFactory extends Factory
{
    protected $model = StatusPage::class;

    public function definition()
    {
        return [
            'id'                 => Str::uuid()->toString(),
            'team_id'            => Team::factory(),
            'token'              => $this->faker->randomNumber(6),
            'title'              => $this->faker->name,
            'meta'               => [],
            'image'              => null,
            'image_content_type' => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }
}
