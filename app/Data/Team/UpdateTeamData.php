<?php

namespace App\Data\Team;

use App\Data\BaseData;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Models\Frontman;

class UpdateTeamData extends BaseData
{
    public ?string $name;
    public ?string $timezone;
    public ?Frontman $defaultFrontman;
    public ?string $dateFormat;
    public ?bool $hasGrantedAccessToSupport;

    public static function fromRequest(UpdateTeamRequest $request): self
    {
        return (new self([
            'name'                      => $request->name,
            'timezone'                  => $request->timezone,
            'defaultFrontman'           => Frontman::find($request->defaultFrontman),
            'dateFormat'                => $request->dateFormat,
            'hasGrantedAccessToSupport' => $request->hasGrantedAccessToSupport,
        ]))->setHasData([
            'name'                      => $request->has('name'),
            'timezone'                  => $request->has('timezone'),
            'defaultFrontman'           => $request->has('defaultFrontman'),
            'dateFormat'                => $request->has('dateFormat'),
            'hasGrantedAccessToSupport' => $request->has('hasGrantedAccessToSupport'),
        ]);
    }
}
