<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition()
    {
        return [
            'team_id'      => Team::factory(),
            'name'         => [
                'en' => 'tag1',
            ],
            'slug'         => [
                'en' => 'tag1',
            ],
            'type'         => 'host',
            'order_column' => 1,
            'meta'         => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }

    public function withTag($tagName)
    {
        return $this->state(function (array $attributes) use ($tagName) {
            return [
                'name' => $tagName,
                'slug' => [
                    'en' => Str::slug($tagName),
                ],
            ];
        });
    }
}
