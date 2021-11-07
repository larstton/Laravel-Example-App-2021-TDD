<?php

namespace App\Data\SubUnit;

use App\Data\BaseData;
use App\Http\Requests\SubUnit\SubUnitRequest;
use App\Http\Requests\SubUnit\UpdateSubUnitRequest;

class SubUnitData extends BaseData
{
    public string $shortId;
    public ?string $name;
    public ?string $information;

    public static function fromStoreRequest(SubUnitRequest $request): self
    {
        return new self([
            'shortId'     => $request->shortId,
            'name'        => $request->name,
            'information' => $request->information,
        ]);
    }

    public static function fromUpdateRequest(UpdateSubUnitRequest $request): self
    {
        return new self([
            'shortId'     => $request->shortId,
            'name'        => $request->name,
            'information' => $request->information,
        ]);
    }
}
