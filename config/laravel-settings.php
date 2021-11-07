<?php

use App\Models\TeamSetting;
use App\Models\UserSetting;
use CloudRadar\LaravelSettings\Defaults\FileDefaultRepository;

return [
    'team' => [
        /*
         * This configuration option is used to tell this package what config files
         * it should care about. Best to use an example to make this clear, but
         * generally speaking you won't need to change it. But if you need to
         * then you need to be aware of what impact it has.
         *
         * By default it's set to 'settings', which means the config files this
         * package will care about should be named using that pre-key. So if
         * you are making a settings file for user-notifications, then it
         * should be named:
         *
         * /config/settings-user-notifications.php
         *
         */
        'config-pre-key'      => 'settings',

        /*
         * This is the model used to persist the settings in the DB. Usually you wouldn't
         * need to overwrite this but if you have a use-case you can do so right here.
         * If you override it be sure to set the table name to settings, or provide
         * your own migration.
         */
        'settings-model'      => TeamSetting::class,

        /*
         * Field name that will be used to query settings for. CUsually something like
         * user_id, team_id, etc..
         */
        'entity-field-name'   => 'team_id',
        /*
         * Field name where settings are stored.
         */
        'settings-field-name' => 'value',
        /*
         * This is the repository for fetching and stored settings.
         */
        'defaults'            => [
            'provider' => FileDefaultRepository::class,
        ],

        ///**
        // * Caching hasn't been implemented yet.... WIP
        // */
        //'cache' => [
        //    'enabled'  => true,
        //    'provider' => \CloudRadar\LaravelSettings\Cache\LaravelCacheRepository::class,
        //],
    ],
    'user' => [
        'config-pre-key'      => 'settings',
        'settings-model'      => UserSetting::class,
        'entity-field-name'   => 'user_id',
        'settings-field-name' => 'value',
        'defaults'            => [
            'provider' => FileDefaultRepository::class,
        ],
    ],
];
