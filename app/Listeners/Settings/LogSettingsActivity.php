<?php

namespace App\Listeners\Settings;

use App\Actions\Team\LogSettingsActivityAction;
use CloudRadar\LaravelSettings\Event\SettingUpdated;

class LogSettingsActivity
{
    private LogSettingsActivityAction $logSettingsActivityAction;

    public function __construct(LogSettingsActivityAction $logSettingsActivityAction)
    {
        $this->logSettingsActivityAction = $logSettingsActivityAction;
    }

    public function handle(SettingUpdated $event)
    {
        $this->logSettingsActivityAction->execute(
            current_user(),
            $event->settingsBeingSet,
            $event->existing,
            $event->storedSettings,
            $event->userId,
        );
    }
}
