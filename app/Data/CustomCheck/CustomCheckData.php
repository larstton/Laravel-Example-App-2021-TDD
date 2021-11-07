<?php

namespace App\Data\CustomCheck;

use App\Data\BaseData;
use App\Http\Requests\CustomCheck\CustomCheckRequest;
use Illuminate\Support\Str;

class CustomCheckData extends BaseData
{
    public string $name;
    public int $expectedUpdateInterval;

    public static function fromRequest(CustomCheckRequest $request): self
    {
        return new self([
            'name'                   => Str::lower($request->input('name')),
            'expectedUpdateInterval' => (int) $request->input('expectedUpdateInterval'),
        ]);
    }
}
