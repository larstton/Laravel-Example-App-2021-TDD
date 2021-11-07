<?php

namespace App\Http\Resources\Team;

use App\Http\Resources\JsonResource;
use App\Models\TeamSetting;

/**
 * @mixin TeamSetting
 */
class TeamSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->value;
    }
}
