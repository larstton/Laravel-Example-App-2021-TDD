<?php

return [

    /*
     * USER SETTING DEFAULTS
     * These values are merged with the DB values to create custom settings for each user.
     */

    'autoShow' => [
        'hostCheckSuccess' => true,
        'agentSetupHelp'   => true,
        'hostGuide'        => true,
    ],

    'dashboard' => [
        'view'                    => 'card_medium',
        'showHostsWithIssuesOnly' => false,
        'shouldAutoRefresh'       => true,
        'refreshRate'             => 60,
        'showMiniCharts'          => false,
    ],

    'host' => [
        'dataTableSettings' => [],
    ],

    'filters' => [
        'host'      => [
            'page'   => 1,
            'limit'  => 10,
            'search' => null,
            'sortBy' => '-date-created',
        ],
        'event'     => [
            'page'   => 1,
            'limit'  => 10,
            'search' => null,
            'sortBy' => '-date-created',
        ],
        'report'    => [
            'page'   => 1,
            'limit'  => 10,
            'search' => null,
            'sortBy' => '-date-created',
        ],
        'recipient' => [
            'page'   => 1,
            'limit'  => 10,
            'search' => null,
            'sortBy' => '-date-created',
        ],
        'teamMember'      => [
            'page'   => 1,
            'limit'  => 10,
            'search' => null,
            'sortBy' => '-date-created',
        ],
    ],

    'makeRecipient' => false,
];
