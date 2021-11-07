<?php

namespace App\Actions\Team;

use App\Models\ActivityLog;
use App\Models\Team;
use App\Models\TeamSetting;
use App\Models\User;

class LogSettingsActivityAction
{
    public function execute(?User $user, $settingsBeingSet, $existing, $storedSettings, $entityId)
    {
        $this->handleSubUnitManagementChange($user, $settingsBeingSet, $existing, $entityId);
    }

    private function handleSubUnitManagementChange(?User $user, $settingsBeingSet, $existing, $entityId)
    {
        if (is_null(optional($settingsBeingSet)['subUnitManagementEnabled'])) {
            return;
        }

        $log = activity()
            ->causedBy($user)
            ->on(Team::find($entityId))
            ->tap(function (ActivityLog $activity) use ($user) {
                $activity->team_id = $user->team_id;
            });

        // If disabling sub-unit management...
        if ($existing['subUnitManagementEnabled'] && ! $settingsBeingSet['subUnitManagementEnabled']) {
            $log->log('Sub-unit management disabled.');
        }

        // If enabling sub-unit management...
        if (! $existing['subUnitManagementEnabled'] && $settingsBeingSet['subUnitManagementEnabled']) {
            $log->log('Sub-unit management enabled.');
        }
    }
}
