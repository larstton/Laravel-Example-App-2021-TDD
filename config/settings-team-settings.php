<?php

return [

    /*
     * TEAM SETTING DEFAULTS
     * These values are merged with the DB values to create custom settings for each user.
     */

    'subUnitManagementEnabled' => true,

    /**
     * --------------------------------------------------------------------
     *                            ** IMPORTANT **
     *       Do no edit these heartbeat defaults without considering
     *                     the impact on the hub system.
     * --------------------------------------------------------------------
     */
    'heartbeats' => [
        'agent'    => [
            'threshold' => 1200,
            'severity'  => 'warning',
            'active'    => true,
        ],
        'frontman' => [
            'threshold' => 1200,
            'severity'  => 'warning',
            'active'    => true,
        ],
        'custom'   => [
            'severity' => 'alert',
            'active'   => true,
        ],
    ],
];
