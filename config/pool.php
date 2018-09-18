<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Pool Connection Name
    |--------------------------------------------------------------------------
    |
    */

    'default' => 'client',

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
        'client' => [
            'timeout' => 1
        ],
    ],
];