<?php

namespace Database\Factories;

use App\Models\SubUnit;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubUnitFactory extends Factory
{
    protected $model = SubUnit::class;

    public function definition()
    {
        return [
            'id'          => Str::uuid()->toString(),
            'team_id'     => Team::factory(),
            'short_id'    => $this->faker->word,
            'name'        => $this->faker->name,
            'information' => null,
            'created_at'  => null,
            'updated_at'  => null,
        ];
    }
}
