<?php

/**
 * @author simon <434730525@qq.com>
 * @datetime 2018-06-28 10:10
 * @link http://www.koodpower.com/
 * @copyright Copyright &copy; 2018 Rights Reserved 快点动力
 */

namespace CrCms\Foundation\Rpc\Client\Connections;

use CrCms\Foundation\Rpc\Client\AbstractConnection;
use CrCms\Foundation\Rpc\Client\Contracts\Connection;

/**
 * Class SocketConnection
 * @package CrCms\Foundation\Rpc\Client\Connections
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