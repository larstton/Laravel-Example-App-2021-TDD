<?php

namespace CloudRadar\LaravelSettings;

use CloudRadar\LaravelSettings\Defaults\DefaultRepository;
use CloudRadar\LaravelSettings\Event\SettingUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class LaravelSettings
{
    /**
     * @var DefaultRepository
     */
    private $defaultRepository;

    private $baseKey;

    private $entityId;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var string
     */
    private $entityFieldName;

    /**
     * @var string
     */
    private $settingFieldName;
    /**
     * @var SettingsConfig
     */
    private $settingsConfig;

    /**
     * LaravelSettings constructor.
     *
     * @param  DefaultRepository  $defaultRepository
     * @param  SettingsConfig  $settingsConfig
     */
    public function __construct(DefaultRepository $defaultRepository, SettingsConfig $settingsConfig)
    {
        $this->defaultRepository = $defaultRepository;
        $this->settingsConfig = $settingsConfig;
        $this->model = $this->settingsConfig->model;
        $this->entityFieldName = $this->settingsConfig->entityFieldName;
        $this->settingFieldName = $this->settingsConfig->settingFieldName;
    }

    public static function setting($value, $label = null, $validator = null)
    {
        return SettingStore::make($value, $label, $validator);
    }

    public function baseKey($baseKey)
    {
        $this->baseKey = $baseKey;

        return $this;
    }

    public function forUser($userId)
    {
        $this->entityId = $userId;

        return $this;
    }

    public function forEntity($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function set(array $newSettings)
    {
        // Fetch defaults and merge in any custom settings for user
        $settings = $this->get()->all();
        $existing = $settings;

        // If current settings we just fetched have the keys from the
        // new settings we're trying to set, then go ahead and set.
        foreach ($newSettings as $key => $value) {
            if (Arr::has($settings, $key)) {
                // If value here is an instance of SettingsStore then use method
                // on object. Otherwise simply set value directly to array...
                if (is_a($currentValue = Arr::get($settings, $key), SettingStore::class, true)) {
                    $currentValue->set($value);
                } else {
                    data_set($settings, $key, $value);
                }
            }
        }

        // Now fetch defaults with no custom changes...
        $defaults = $this->defaultRepository->get($this->baseKey);

        // Now remove all defaults from the new settings array we
        // created above. Whatever is left over needs persisting.
        $forStoring = $this->arrayRecursiveDiff($settings, $defaults->all());

        // Get ALL stored settings for user.
        $allStoredSettings = optional($this->getStoredSettings())[$this->settingFieldName];

        // if we have nothing to store and stored settings is not null...
        if (empty($forStoring) && ! is_null($allStoredSettings) && $allStoredSettings[$this->baseKey]) {
            unset($allStoredSettings[$this->baseKey]);
        } else {
            if (! empty($forStoring)) {
                $allStoredSettings[$this->baseKey] = $forStoring;
            }
        }

        // if $allStoredSettings is empty then we can delete settings for user
        // else save...
        if (empty($allStoredSettings)) {
            $this->deleteSettingsForEntity();
        } else {
            $this->storeSettings($allStoredSettings);
        }

        SettingUpdated::dispatch($newSettings, $existing, $allStoredSettings, $this->entityId);
    }

    public function get($key = null): Collection
    {
        $settings = $this->defaultRepository->get($this->makeKey($key));

        if (! is_null($this->entityId) && $this->entityHasStoredSettingsForBaseKey()) {

            // get ALL stored settings for user
            $storedSettings = $this->getStoredSettings()->getAttribute($this->settingFieldName)[$this->baseKey];

            // does stored settings have the key we are wanting?
            if (is_null($key) || Arr::has($storedSettings, $key)) {
                $settings = $this->arrayRecursiveReplace($settings->all(), $storedSettings);
            }
        }

        return $settings;
    }

    private function makeKey($key = null)
    {
        return $this->baseKey.(! is_null($key) ? '.'.$key : '');
    }

    /**
     * @return bool
     */
    private function entityHasStoredSettingsForBaseKey(): bool
    {
        return $this->model::where($this->entityFieldName, $this->entityId)
                ->where($this->settingFieldName, 'LIKE', "%{$this->baseKey}%")
                ->count() > 0;
    }

    /**
     * @return mixed
     */
    private function getStoredSettings()
    {
        return $this->model::where($this->entityFieldName, $this->entityId)->first();
    }

    public function arrayRecursiveReplace($settings, $fromStorage): Collection
    {
        foreach (Arr::dot($fromStorage) as $key => $value) {
            if (Arr::has($settings, $key)) {
                if (is_a($fromDefault = Arr::get($settings, $key), SettingStore::class, true)) {
                    Arr::set($settings, $key, $fromDefault->set($value));
                } else {
                    Arr::set($settings, $key, $value);
                }
            } else {
                Arr::set($settings, $key, $value);
            }
        }

        return collect($settings);
    }

    private function arrayRecursiveDiff($newSettings, $defaultSettings)
    {
        $storeTheseSettings = [];
        foreach ($newSettings as $newSettingKey => $newSettingValue) {
            if (array_key_exists($newSettingKey, $defaultSettings)) {
                if (is_array($newSettingValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($newSettingValue, $defaultSettings[$newSettingKey]);
                    if (count($aRecursiveDiff)) {
                        $storeTheseSettings[$newSettingKey] = $aRecursiveDiff;
                    }
                } else {
                    if (is_a($newSettingValue, SettingStore::class, true)) {
                        if (! $newSettingValue->compareValues($defaultSettings[$newSettingKey])) {
                            /** @var SettingStore $newSettingValue */
                            $storeTheseSettings[$newSettingKey] = $newSettingValue->getValue();
                        }
                    } else {
                        if ($newSettingValue !== $defaultSettings[$newSettingKey]) {
                            $storeTheseSettings[$newSettingKey] = $newSettingValue;
                        }
                    }
                }
            } else {
                $storeTheseSettings[$newSettingKey] = $newSettingValue;
            }
        }

        return $storeTheseSettings;
    }

    private function deleteSettingsForEntity()
    {
        return $this->model::destroy($this->entityId);
    }

    private function storeSettings($settings)
    {
        return $this->model::updateOrCreate([$this->entityFieldName => $this->entityId], [
            $this->entityFieldName  => $this->entityId,
            $this->settingFieldName => $settings,
        ]);
    }
}
