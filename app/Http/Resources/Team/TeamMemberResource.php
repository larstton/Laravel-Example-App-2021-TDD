<?php

namespace App\Http\Resources\Team;

use App\Http\Resources\JsonResource;
use App\Http\Transformers\DateTransformer;
use App\Models\TeamMember;

/**
 * @mixin TeamMember
 */
class TeamMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'role'       => $this->role->value,
            'teamStatus' => $this->team_status->value,
            'nickname'   => $this->nickname,
            'hostTag'    => $this->host_tag,
            'subUnit'    => $this->sub_unit_id,
            'notes'      => $this->notes,
            'dates'      => [
                'updatedAt' => DateTransformer::transform($this->updated_at),
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
