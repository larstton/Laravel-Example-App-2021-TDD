<?php

namespace Database\Seeders;

use App\Models\Frontman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class FrontmanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::fakeFor(function() {
            Frontman::updateOrCreate([
                'id' => '169502d5-a541-49bb-9782-4cf4d71148cf',
            ], [
                'location'          => 'INDIA-SOUTH-Mumbai',
                'password'          => 'dahkis0pheiphoo1Ai',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => '17bdd2bb-c364-4748-a7a8-2799a8501e8f',
            ], [
                'location'          => 'US-WEST-Oregon',
                'password'          => 'ohgh0ea8oong0Iephu',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => '24995c49-45ba-43d6-9205-4f5e83d32a11',
            ], [
                'location'          => 'EU-WEST-Netherlands',
                'password'          => 'aeraeB6zuthu3enoof',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => '3f74714c-b896-44df-ab3e-c04ca27f609b',
            ], [
                'location'          => 'ASIA-SOUTHEAST-Singapore',
                'password'          => 'cheeroshaedaCh3ez5',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => '82335bb7-e5ce-4a16-bf7a-f431172aa4a8',
            ], [
                'location'          => 'AU-SOUTHEAST-Sydney',
                'password'          => 'dohdeiw4riir2Ohbex',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => '8cbf4026-889e-4b84-b9b5-b0586061e1c4',
            ], [
                'location'          => 'US-EAST-Virginia',
                'password'          => 'Xa5peiS3quia4ohpuu',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
            Frontman::updateOrCreate([
                'id' => 'f9572610-1e86-4cea-a365-5079431ac6da',
            ], [
                'location'          => 'BRASIL-SOUTH-Sao Paulo',
                'password'          => 'eiRae7uazaiJohng1n',
                'team_id'           => '00000000-0000-0000-0000-000000000000',
                'last_heartbeat_at' => now()->subDays(1),
                'user_id'           => Str::uuid()->toString(),
            ]);
        });
    }
}
