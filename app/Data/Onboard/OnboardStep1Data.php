<?php

namespace App\Data\Onboard;

use App\Data\BaseData;
use App\Models\Frontman;
use Illuminate\Http\Request;

class OnboardStep1Data extends BaseData
{
    public string $timezone;
    public Frontman $defaultFrontman;
    public string $dateFormat;

    public static function make(Request $request): self
    {
        return new self([
            'timezone'        => $request->timezone,
            'defaultFrontman' => Frontman::find($request->defaultFrontman),
            'dateFormat'      => $request->dateFormat,
        ]);
    }
}
