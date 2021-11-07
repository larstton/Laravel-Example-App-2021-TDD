<?php

namespace App\Actions\Team;

use App\Data\Team\UpdateTeamData;
use App\Models\Team;

class UpdateTeamAction
{
    public function execute(Team $team, UpdateTeamData $data): Team
    {
        $team->update([
            'name'                          => $data->get('name', $team->name),
            'timezone'                      => $data->get('timezone', $team->timezone),
            'default_frontman_id'           => optional(
                $data->get('defaultFrontman', $team->defaultFrontman)
            )->id,
            'date_format'                   => $data->get('dateFormat', $team->date_format),
            'has_granted_access_to_support' => $data->get(
                'hasGrantedAccessToSupport',
                $team->has_granted_access_to_support
            ),
        ]);

        return $team;
    }
}
