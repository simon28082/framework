<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 20:42
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Connectors;


use CrCms\Foundation\Rpc\Client\Contracts\Connector;
use CrCms\Foundation\Swoole\Socket\Client;

class SocketConnector implements Connector
{
    public function connect(array $config)
    {
        new Client(
            new \Swoole\Coroutine\Client()
        );
    }


}