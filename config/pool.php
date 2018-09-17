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
            'factory' => \CrCms\Foundation\Client\Http\Guzzle\Factory::class,
//                'host' => 'user.rpc.crcms.local',
//                'port' => 80,
            'settings' => [
                'timeout' => 1
            ],
        ],
    ],
];