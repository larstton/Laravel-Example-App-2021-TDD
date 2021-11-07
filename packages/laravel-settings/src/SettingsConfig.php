<?php

namespace CloudRadar\LaravelSettings;

use Illuminate\Database\Eloquent\Model;

class SettingsConfig
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $entityFieldName;

    /**
     * @var string
     */
    public $settingFieldName;
    /**
     * SettingsConfig constructor.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model, $settingFieldName, $entityFieldName)
    {
        $this->model = $model;
        $this->settingFieldName = $settingFieldName;
        $this->entityFieldName = $entityFieldName;
    }
}
