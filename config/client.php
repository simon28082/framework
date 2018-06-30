<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:19
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

return [

    'default' => 'socket',

    'connections' => [
        'socket' => [
            /*[
                'driver' => 'socket',
                'host' => '127.0.0.1',
                'port' => 22,
                'settings' => [
                    'timeout' => 10
                ],

            ],*/
            [
                'driver' => 'http',
                'host' => 'baidu.com',
                'port' => 80,
                'settings' => [
                    'timeout' => 10
                ],
            ],

        ]
    ],

    'selector' => \CrCms\Foundation\Client\Selectors\RandSelector::class,

    /*'drivers' => [
        'socket' => [
            'settings' => [],
        ]
    ],*/
];