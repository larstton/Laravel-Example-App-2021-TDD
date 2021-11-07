<?php

namespace App\Data\SnmpCheck;

use App\Data\BaseData;
use App\Http\Requests\SnmpCheck\UpdateSnmpCheckRequest;

class UpdateSnmpCheckData extends BaseData
{
    public ?string $preset;
    public ?int $checkInterval;
    public ?bool $active;

    public static function fromRequest(UpdateSnmpCheckRequest $request): self
    {
        return new self([
            'preset'        => $request->preset,
            'checkInterval' => self::nullableIntCast($request->checkInterval),
            'active'        => self::nullableBoolCast($request->active),
        ]);
    }
}
