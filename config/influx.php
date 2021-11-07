<?php

return [

    'repository' => [

        'host'       => env('INFLUX_HOST', 'localhost'),
        'port'       => env('INFLUX_PORT', '8086'),
        'username'   => env('INFLUX_USERNAME', ''),
        'password'   => env('INFLUX_PASSWORD', ''),

        /*
         * List of databases we have available in Influx.
         * Left side is the alias used on the frontend.
         * Right side is the db name inside Influx.
         */
        'databases'  => env('INFLUX_DATABASES', [
            'customChecks' => 'customChecksResults',
            'check'        => 'checkResults',
            'cagent'       => 'checkResults',
            'frontman'     => 'checkResults',
        ]),

        /*
         * When no DB supplied, this is the default one used in requests for Influx data.
         */
        'default_db' => env('INFLUX_DEFAULT_DATABASE', 'checkResults'),

    ],

    'query-builder' => [

        'parameters' => [
            'filter' => 'filter',
            'sort'   => 'sort',
            'append' => 'append'
        ],

    ],

];
