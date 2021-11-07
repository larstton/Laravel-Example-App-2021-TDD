<?php

namespace App\Data\ServiceCheck;

use App\Data\BaseData;
use App\Http\Requests\ServiceCheck\CreateServiceCheckRequest;

class ServiceCheckData extends BaseData
{
    public string $protocol;
    public int $checkInterval;
    public ?string $service;
    public ?int $port;
    public bool $active;
    public ?bool $preflight;

    public static function fromRequest(CreateServiceCheckRequest $request): self
    {
        return new self([
            'protocol'      => $request->input('protocol'),
            'checkInterval' => (int) $request->input('checkInterval'),
            'service'       => $request->input('service'),
            'port'          => self::nullableIntCast($request->input('port')),
            'active'        => (bool) $request->input('active'),
            'preflight'     => (bool) $request->input('preflight', false),
        ]);
    }
}
