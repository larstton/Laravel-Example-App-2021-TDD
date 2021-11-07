<?php

namespace App\Actions\WebCheck;

use App\Actions\WebCheck\Guard\WebCheckUniquenessGuard;
use App\Data\WebCheck\WebCheckData;
use App\Enums\CheckLastSuccess;
use App\Models\Host;
use App\Models\WebCheck;
use App\Support\Preflight\Contract\CheckPreflight;

class UpdateWebCheckAction
{
    private CheckPreflight $checkPreflight;

    public function __construct(CheckPreflight $checkPreflight)
    {
        $this->checkPreflight = $checkPreflight;
    }

    public function execute(WebCheck $webCheck, Host $host, WebCheckData $webCheckData): WebCheck
    {
        $this->guard($webCheck, $webCheckData, $host);

        // TODO Id rather this returns an object where we can check the result and get messages etc
        // throwing an exception to control logic feels icky.
        $this->checkPreflight->webCheck($host, $webCheckData);

        $webCheck->update([
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
            'last_success'              => CheckLastSuccess::Pending(),
        ]);

        return $webCheck;
    }

    private function guard(WebCheck $webCheck, WebCheckData $webCheckData, Host $host)
    {
        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host, $webCheck);
    }
}
