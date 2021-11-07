<?php

namespace App\Actions\Wizard;

use App\Actions\Host\CreateHostAction;
use App\Actions\WebCheck\CreateWebCheckAction;
use App\Data\Host\HostData;
use App\Data\WebCheck\WebCheckData;
use App\Data\Wizard\CreateWebCheckWizardData;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;
use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateWebCheckFromWizardAction
{
    private ?Host $host;

    private CreateHostAction $createHostAction;

    private CreateWebCheckAction $createWebCheckAction;

    public function __construct(
        CreateHostAction $createHostAction,
        CreateWebCheckAction $createWebCheckAction
    ) {
        $this->createHostAction = $createHostAction;
        $this->createWebCheckAction = $createWebCheckAction;
    }

    public function execute(User $user, CreateWebCheckWizardData $data): WebCheck
    {
        $webCheckData = $this->constructWebCheckDataObject($data,
            $parsedUrlData = parse_url($data->url),
        );

        $connect = $parsedUrlData['host'];

        $this->host = Host::where('connect', $connect)
            ->with([
                'webChecks' => function ($query) use ($webCheckData) {
                    $query
                        ->where('protocol', $webCheckData->protocol)
                        ->where('method', $webCheckData->method)
                        ->where('path', $webCheckData->path)
                        ->where('port', $webCheckData->port);
                },
            ])
            ->first();

        $this->runGuards($connect);

        return DB::transaction(function () use ($user, $connect, $webCheckData) {
            if (! $this->host) {
                $this->host = $this->createHostAction->execute($user, new HostData([
                    'name'        => $connect,
                    'description' => 'Created by web check wizard',
                    'connect'     => $connect,
                    'cagent'      => false,
                    'dashboard'   => true,
                    'muted'       => false,
                    'active'      => true,
                    'snmpData'    => [],
                ]));
            }

            return $this->createWebCheckAction->execute($user, $this->host, $webCheckData);
        });
    }

    private function constructWebCheckDataObject(CreateWebCheckWizardData $data, array $parsedUrlData): WebCheckData
    {
        $webCheckData = [
            'protocol'            => $parsedUrlData['scheme'],
            'method'              => 'GET',
            'path'                => Arr::get($parsedUrlData, 'path', '/'),
            'port'                => Arr::get($parsedUrlData, 'port',
                fn () => $parsedUrlData['scheme'] === 'https' ? 443 : 80
            ),
            'timeOut'             => 5.0,
            'checkInterval'       => 90,
            'expectedHttpStatus'  => 200,
            'active'              => true,
            'dontFollowRedirects' => false,
            'ignoreSSLErrors'     => true,
            'searchHtmlSource'    => false,
            'preflight'           => (bool) $data->preflight,
        ];

        if (Arr::has($parsedUrlData, 'query')) {
            $webCheckData['path'] .= "?{$parsedUrlData['query']}";
        }

        return new WebCheckData($webCheckData);
    }

    private function runGuards($connect)
    {
        $hostExists = ! is_null($this->host);

        if ($hostExists && $this->host->webChecks->isNotEmpty()) {
            fail_validation('url', 'This web-check already exists.');
        }

        if (! $hostExists && ! $this->isPublicConnect($connect)) {
            fail_validation('url', $connect.' must be a publicly resolvable IP address or FQDN.');
        }
    }

    private function isPublicConnect($connect): bool
    {
        return IpAddress::isValidPublicIP($connect) || FQDN::isValidPublicFQDN($connect);
    }
}
