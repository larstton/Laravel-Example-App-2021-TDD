<?php

return [

    'languages' => [
        'en', 'de', 'es', 'pt',
    ],

    'plans' => [
        'payg' => [
            'daily_rate' => [
                'euro' => 0.07,
                'usd'  => 0.08,
            ],
        ],
    ],

    'notifier' => [
        'base_url' => env('NOTIFIER_URL'),
        'timeout'  => 5,
        'headers'  => [
            'User-Agent' => 'Cloudradar hub/1.0',
        ],
        'username' => env('NOTIFIER_USER'),
        'password' => env('NOTIFIER_PASSWORD'),
    ],

    'checkout' => [
        'base_url'  => env('CHECKOUT_URL'),
        'timeout'   => 5,
        'headers'   => [
            'User-Agent' => 'Cloudradar dashboard/1.0',
        ],
        'username'  => env('CHECKOUT_USER'),
        'password'  => env('CHECKOUT_PASSWORD'),
        'jwt_token' => env('CHECKOUT_JWT_TOKEN'),
    ],

    'support' => [
        'support_email' => 'support@cloudradar.freshdesk.com',
    ],

    'frontman' => [
        'base_frontmen' => [
            'eu_west' => '24995c49-45ba-43d6-9205-4f5e83d32a11',
        ],
    ],

    'salt' => env('CLOUDRADAR_SALT', '87whdf4oinq!@$wh3£%$^_4f8o7jw4hf87jh'),

    'loophole' => [
        'username' => env('LOOPHOLE_USERNAME'),
        'password' => env('LOOPHOLE_PASSWORD'),
    ],

    'gitbook' => [
        'base_url' => 'https://api-beta.gitbook.com/v1/',
        'token'    => env('GITBOOK_TOKEN'),
        'space_id' => env('GITBOOK_SPACE_ID'),
    ],

    'esendex' => [
        'base_url' => 'https://admin.api.esendex.com/v1.0/',
        'timeout'  => 5,
    ],

    'msteams' => [
        'timeout' => 5,
    ],

];
