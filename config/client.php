<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:19
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

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
            [
                'driver' => 'http',
                'host' => '192.168.1.108',
                'port' => 2222,
                'settings' => [
                    'timeout' => 10
                ],
            ],
            [
                'driver' => 'http',
                'host' => '192.168.1.118',
                'port' => 2222,
                'settings' => [
                    'timeout' => 0.5
                ],
            ]
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Connection pool selector
    |--------------------------------------------------------------------------
    |
    | Different selectors can be selected to select the connection in the connection pool
    | RandSelector: Randomly select an available selector
    | RingSelector: A ring selector to ensure scheduling equalization
    | ResidentSelector: Always use the same available selector
    | PopSelector: Swoole coroutines are used, each time an independently generated connection
    */

    'selector' => CrCms\Foundation\Client\Selectors\PopSelector::class,
];