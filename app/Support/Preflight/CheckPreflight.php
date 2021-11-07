<?php

namespace App\Support\Preflight;

use App\Data\ServiceCheck\ServiceCheckData;
use App\Data\WebCheck\WebCheckData;
use App\Exceptions\CheckPreflightException;
use App\Models\Host;
use App\Support\Preflight\Contract\CheckPreflight as CheckPreflightContract;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckPreflight implements CheckPreflightContract
{
    public static function serviceCheck(Host $host, ServiceCheckData $check): bool
    {
        if (! $check->preflight) {
            // No preflight wanted for this check
            return true;
        }

        if ($host->frontman->isPrivate()) {
            // We can't perform pre-flights inside a private intranet
            return true;
        }

        if ($check->protocol === 'icmp' && $check->service === 'ping') {
            return self::fping($host->connect);
        }

        if ($check->protocol === 'tcp') {
            return self::netcat($host->connect, $check->port);
        }

        if ($check->protocol === 'udp') {
            return self::udpCheck($host->connect, $check);
        }

        if ($check->protocol === 'ssl') {
            return self::sslCertCheck($host->connect, $check->service, $check->port);
        }

        throw new CheckPreflightException('Ping failed');
    }

    private static function fping($connect)
    {
        $console['cmd'] = sprintf('fping -r2 %s', $connect);
        $console['cmdResult'] = shell_exec($console['cmd'].' 2>&1');

        if (Str::contains($console['cmdResult'], 'is alive')) {
            return true;
        }

        throw new CheckPreflightException("ICMP Ping to {$connect} failed.", $console);
    }

    private static function netcat($connect, $port)
    {
        if (! is_int($port)) {
            throw new CheckPreflightException(sprintf('Port %s not testable.', $port));
        }
        $console['cmd'] = sprintf('nc -v -w 3 -z %s %s', $connect, $port);
        $console['cmdResult'] = shell_exec($console['cmd'].' 2>&1');
        if (Str::contains($console['cmdResult'], 'succeeded')) {
            return true;
        }

        throw new CheckPreflightException("TCP Port check for {$connect}:{$port} failed.", $console);
    }

    private static function udpCheck($connect, ServiceCheckData $check)
    {
        $serviceCheck = [
            'connect'  => $connect,
            'protocol' => 'udp',
            'service'  => $check->service,
            'port'     => $check->port,
        ];
        $frontmanResult = self::frontmanExecute($serviceCheck, 'serviceChecks');

        if (Str::length($frontmanResult['message']) === 0) {
            return true;
        }

        $console['cmd'] = $check->service.'-connect '.$connect.' '.$check->port.'';
        $console['cmdResult'] = $frontmanResult['message'];

        throw new CheckPreflightException(
            "UDP Port check for {$connect}:{$check->port} failed.",
            $console
        );
    }

    private static function frontmanExecute(array $check, string $type)
    {
        throw_unless(in_array($type, ['webChecks', 'serviceChecks']),
            new Exception('Unknown check type. Only webChecks and serviceChecks possible.')
        );

        $tmpID = uniqid();
        $tmpInputFile = sprintf('/tmp/frontman-%s-%s.json', $type, $tmpID);

        $cmd = [
            'frontman',
            sprintf(' -c %s', storage_path('site/frontman.conf')),
            sprintf(' -i %s', $tmpInputFile),
            ' -r -o -',
            ' 2>&1',
        ];

        // Create a temporary file for frontman which serves as input to execute the check once
        file_put_contents($tmpInputFile, json_encode(
            [
                $type => [
                    [
                        'checkUUID' => $tmpID,
                        'check'     => $check,
                    ],
                ],
            ]
        ));

        // Execute frontman once
        $shellReturn = shell_exec(implode(' ', $cmd));
        Storage::disk('local')->delete($tmpInputFile);

        // Decode the result.
        foreach (json_decode($shellReturn, true) as $checkResult) {
            if ($tmpID == $checkResult['checkUuid']) {
                return $checkResult;
            }
        }
    }

    private static function sslCertCheck($connect, $service, $port)
    {
        $serviceCheck = [
            'connect'  => $connect,
            'protocol' => 'ssl',
            'service'  => $service,
            'port'     => $port,
        ];

        $frontmanResult = self::frontmanExecute($serviceCheck, 'serviceChecks');

        if (Str::length($frontmanResult['message']) === 0) {
            return true;
        }

        throw new CheckPreflightException($frontmanResult['message'], [$frontmanResult['message']]);
    }

    public static function webCheck(Host $host, WebCheckData $check): bool
    {
        if (! $check->preflight) {
            // No preflight wanted for this check
            return true;
        }

        $url = "{$check->protocol}://{$host->connect}";

        if (isset($check->port)) {
            $url .= ':'.$check->port;
        }
        if (isset($check->path)) {
            $url .= $check->path;
        } else {
            $url .= '/';
        }
        $method = strtolower($check->method);
        $check = get_object_vars($check);
        $frontmanCheck = [
            'url'    => $url,
            'method' => $method,
        ];
        foreach ([
                     'expectedHttpStatus', 'expectedPattern', 'expectedPatternPresence',
                     'dontFollowRedirects', 'ignoreSSLErrors', 'searchHtmlSource',
                     'headers', 'timeOut',
                 ] as $key) {
            if (array_key_exists($key, $check)) {
                if ('timeOut' === $key) {
                    $frontmanCheck['timeout'] = $check[$key];
                } else {
                    $frontmanCheck[$key] = $check[$key];
                }
            }
        }

        $frontmanResult = self::frontmanExecute($frontmanCheck, 'webChecks');

        if (is_array($frontmanResult) && ((bool) $frontmanResult['measurements']['http.'.$method.'.success'])) {
            return true;
        }

        $console['cmd'] = 'curl -v -X '.strtoupper($method).' '.$url;
        $statusCode = Arr::get($frontmanResult['measurements'], "http.{$method}.httpStatusCode", '?');
        $console['cmdResult'] = 'HTTP STATUS: '.$statusCode."\n";
        $console['cmdResult'] .= $frontmanResult['message'];

        throw new CheckPreflightException('Web check preflight failed', $console);
    }
}
