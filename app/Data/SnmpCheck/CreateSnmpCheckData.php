<?php

namespace App\Data\SnmpCheck;

use App\Data\BaseData;
use App\Http\Requests\SnmpCheck\CreateSnmpCheckRequest;

class CreateSnmpCheckData extends BaseData
{
    public string $preset;
    public int $checkInterval;
    public bool $active;

    public static function fromRequest(CreateSnmpCheckRequest $request): self
    {
        return new self([
            'preset'        => $request->input('preset'),
            'checkInterval' => (int) $request->input('checkInterval'),
            'active'        => (bool) $request->input('active'),
        ]);
    }
}
