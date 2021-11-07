<?php

namespace Database\Factories;

use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'id'                => Str::uuid()->toString(),
            'email'             => $this->faker->unique()->safeEmail,
            'password'          => '1234567',
            'role'              => TeamMemberRole::Admin(),
            'team_status'       => TeamStatus::Joined(),
            'terms_accepted'    => true,
            'privacy_accepted'  => true,
            'product_news'      => true,
            'nickname'          => null,
            'name'              => null,
            'host_tag'          => null,
            'lang'              => 'en',
            'team_id'           => Team::factory(),
            'sub_unit_id'       => null,
            'notes'             => null,
            'remember_token'    => null,
            'email_verified_at' => null,
            'created_at'        => null,
            'updated_at'        => null,
        ];
    }
}
