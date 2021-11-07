<?php

namespace App\Actions\WebCheck;

use App\Actions\WebCheck\Guard\WebCheckUniquenessGuard;
use App\Data\WebCheck\WebCheckData;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;
use App\Support\Preflight\Contract\CheckPreflight;
use App\Support\Rule\RuleFactory;
use Illuminate\Support\Facades\DB;

class CreateWebCheckAction
{
    private CheckPreflight $checkPreflight;

    public function __construct(CheckPreflight $checkPreflight)
    {
        $this->checkPreflight = $checkPreflight;
    }

    public function execute(User $user, Host $host, WebCheckData $webCheckData): WebCheck
    {
        $this->guard($webCheckData, $host);

        // TODO Id rather this returns an object where we can check the result and get messages etc
        // throwing an exception to control logic feels icky.
        $this->checkPreflight->webCheck($host, $webCheckData);

        return DB::transaction(function () use ($webCheckData, $user, $host) {
            $webCheck = $host->webChecks()->create([
                'user_id'                   => $user->id,
                'protocol'                  => $webCheckData->protocol,
                'path'                      => $webCheckData->path,
                'port'                      => $webCheckData->port,
                'method'                    => $webCheckData->method,
                'post_data'                 => $webCheckData->postData,
                'dont_follow_redirects'     => $webCheckData->dontFollowRedirects,
                'ignore_ssl_errors'         => $webCheckData->ignoreSSLErrors,
                'search_html_source'        => $webCheckData->searchHtmlSource,
                'expected_pattern'          => $webCheckData->expectedPattern,
                'expected_pattern_presence' => $webCheckData->expectedPatternPresence,
                'expected_http_status'      => $webCheckData->expectedHttpStatus,
                'time_out'                  => $webCheckData->timeOut,
                'active'                    => $webCheckData->active,
                'headers'                   => $webCheckData->headers,
                'headers_md5_sum'           => $webCheckData->headersMD5,
                'check_interval'            => $webCheckData->checkInterval,
            ]);

            RuleFactory::makeHttpPerformanceWarningRule($user)->saveIfNew($host);

            return $webCheck;
        });
    }

    private function guard(WebCheckData $webCheckData, Host $host)
    {
        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }
}
