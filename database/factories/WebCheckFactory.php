<?php

namespace Database\Factories;

use App\Enums\CheckLastSuccess;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebCheckFactory extends Factory
{
    protected $model = WebCheck::class;

    public function definition()
    {
        return [
            'id'                        => Str::uuid()->toString(),
            'host_id'                   => Host::factory(),
            'user_id'                   => User::factory(),
            'path'                      => '/',
            'protocol'                  => 'https',
            'port'                      => 80,
            'expected_pattern'          => null,
            'expected_pattern_presence' => 'present',
            'expected_http_status'      => 200,
            'search_html_source'        => false,
            'time_out'                  => 5.0,
            'ignore_ssl_errors'         => false,
            'check_interval'            => 60,
            'dont_follow_redirects'     => false,
            'method'                    => 'GET',
            'active'                    => true,
            'in_progress'               => false,
            'last_success'              => CheckLastSuccess::Pending(),
            'last_message'              => null,
            'post_data'                 => null,
            'headers'                   => null,
            'headers_md5_sum'           => null,
            'last_checked_at'           => null,
            'created_at'                => null,
            'updated_at'                => null,
        ];
    }
}
