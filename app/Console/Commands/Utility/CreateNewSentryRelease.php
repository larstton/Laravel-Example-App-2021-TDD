<?php

namespace App\Console\Commands\Utility;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateNewSentryRelease extends Command
{
    protected $signature = 'cloudradar:sentry:create-release';

    public function handle()
    {
        $sentryBearerToken = '9c9ab1c3a5f04f92872f7b153256f00c59afee52b7cb4829bf368b4b670bcb0f';
        $environment = app()->environment();
        $version = get_commit_hash();
        $repository = 'cloudradar/core_v3';
        $commit = get_commit_hash();
        $previousCommit = get_previous_commit_hash();
        $projects = 'core';

        $headers = [
            'Authorization' => "Bearer {$sentryBearerToken}",
            'Content-Type'   => 'application/json',
        ];

        Http::withHeaders($headers)
            ->post('https://sentry.io/api/0/organizations/cloudradar/releases/', [
                'environment' => $environment,
                'version'     => $version,
                'refs'        => [
                    'repository'     => $repository,
                    'commit'         => $commit,
                    'previousCommit' => $previousCommit,
                ],
                'projects'    => $projects,
            ]);

        Http::withHeaders($headers)
            ->post("https://sentry.io/api/0/organizations/cloudradar/releases/{$commit}/deploys/", [
                'environment' => $environment,
            ]);
    }
}
