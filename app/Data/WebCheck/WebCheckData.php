<?php

namespace App\Data\WebCheck;

use App\Data\BaseData;
use App\Http\Requests\WebCheck\WebCheckRequest;

class WebCheckData extends BaseData
{
    public string $path;
    public string $protocol;
    public ?int $port;
    public string $method;
    public ?string $expectedPattern;
    public string $expectedPatternPresence = 'present';
    public ?int $expectedHttpStatus;
    public ?bool $ignoreSSLErrors;
    public ?float $timeOut;
    public ?bool $dontFollowRedirects;
    public ?bool $searchHtmlSource;
    public int $checkInterval;
    public bool $active;
    public ?bool $preflight;
    public ?string $postData;
    public ?array $headers;
    public ?string $headersMD5;

    public static function fromRequest(WebCheckRequest $request): self
    {
        return new self([
            'path'                    => $request->input('path'),
            'protocol'                => $request->input('protocol'),
            'port'                    => self::nullableIntCast($request->input('port')),
            'method'                  => $request->input('method'),
            'expectedPattern'         => $request->input('expectedPattern'),
            'expectedPatternPresence' => $request->input('expectedPatternPresence', 'present'),
            'expectedHttpStatus'      => self::nullableIntCast($request->input('expectedHttpStatus')),
            'ignoreSSLErrors'         => (bool) $request->input('ignoreSSLErrors', false),
            'timeOut'                 => (float) $request->input('timeOut'),
            'dontFollowRedirects'     => (bool) $request->input('dontFollowRedirects', false),
            'searchHtmlSource'        => (bool) $request->input('searchHtmlSource', false),
            'checkInterval'           => (int) $request->input('checkInterval'),
            'active'                  => (bool) $request->input('active'),
            'preflight'               => (bool) $request->input('preflight', false),
            'postData'                => $request->input('postData'),
            'headers'                 => $getWebCheckHeaders = $request->getWebCheckHeaders(),
            'headersMD5'              => md5(json_encode($getWebCheckHeaders)),
        ]);
    }
}
