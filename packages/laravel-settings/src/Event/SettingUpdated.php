<?php

namespace CloudRadar\LaravelSettings\Event;

use Illuminate\Foundation\Events\Dispatchable;

class SettingUpdated
{
    use Dispatchable;

    public $existing;

    public $userId;

    public $storedSettings;

    public $settingsBeingSet;

    /**
     * SettingUpdated constructor.
     *
     * @param $settingsBeingSet
     * @param $existing
     * @param $storedSettings
     * @param $userId
     */
    public function __construct($settingsBeingSet, $existing, $storedSettings, $userId)
    {
        $this->settingsBeingSet = $settingsBeingSet;
        $this->existing = $existing;
        $this->storedSettings = $storedSettings;
        $this->userId = $userId;
    }
}
