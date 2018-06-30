<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connections;

use CrCms\Foundation\Client\AbstractConnection;
use CrCms\Foundation\Client\Contracts\Connection;

/**
 * Class SocketConnection
 * @package CrCms\Foundation\Client\Connections
 */
class SocketConnection extends AbstractConnection implements Connection
{
    public function send(string $data): bool
    {
        return $this->connector->send('abc');
    }

    public function recv(): string
    {
        // TODO: Implement recv() method.
    }
}