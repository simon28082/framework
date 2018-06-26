<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Contracts\Connection;
use CrCms\Foundation\Rpc\Client\Contracts\ConnectionPool as ConnectionPoolContract;
use ArrayAccess;
use CrCms\Foundation\Rpc\Client\Contracts\Selector;

class ConnectionPool implements ConnectionPoolContract, ArrayAccess
{
    /**
     * @var array
     */
    protected $connections;

    protected $selector;

    protected $deathConnections;

    public function __construct(array $connections, Selector $selector)
    {
        $this->selector = $selector;
        $this->connections = $connections;
    }

    public function nextConnection(): Connection
    {
        return $this->selector->select($this->connections);
    }

    public function deathConnection()
    {
        foreach ($this->connections as $key => $connection) {
            if ($connection->isAlive() === false) {
                $connection->disconnectTime(time());
                $this->deathConnections[] = $connection;
                unset($this->connections[$key]);
            }
        }
    }


    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }


}