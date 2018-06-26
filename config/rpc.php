<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:19
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

return [

    'default' => ['socket'],

    'connections' => [
        'socket' => [
            [
                'driver' => 'socket',
                'host' => '127.0.0.1',
                'port' => 22,
                'timeout' => 10
            ],
            [
                'driver' => 'socket',
                'host' => '127.0.0.2',
                'port' => 22,
                'timeout' => 10
            ],

        ]
    ]

];