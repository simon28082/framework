<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Pool Connection Name
    |--------------------------------------------------------------------------
    |
    */

    'default' => 'http',

    /*
    |--------------------------------------------------------------------------
    | Connection Pool Connections
    |--------------------------------------------------------------------------
    |
    | Connection pools are divided into different connection groups
    | Each connection group can have multiple connections
    | Determine which pool's connection is currently used by selecting a connection group
    |
    */

    'connections' => [
        'http' => [
            'driver' => 'guzzle_http',
//                'host' => 'user.rpc.crcms.local',
//                'port' => 80,
            'settings' => [
                'timeout' => 1
            ],
        ],
    ],
];