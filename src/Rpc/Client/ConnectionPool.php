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
use Illuminate\Support\Arr;
use BadMethodCallException;

/**
 * Class ConnectionPool
 * @package CrCms\Foundation\Rpc\Client
 */
class ConnectionPool implements ConnectionPoolContract, ArrayAccess
{
    /**
     * @var Selector
     */
    protected $selector;

    /**
     * @var array
     */
    protected $deathConnectionGroups = [];

    /**
     * @var array
     */
    protected $connectionGroups = [];

    /**
     * ConnectionPool constructor.
     * @param Selector $selector
     * @param null|string $group
     * @param array $connections
     */
    public function __construct(Selector $selector, ?string $group = null, array $connections = [])
    {
        $this->selector = $selector;

        if (!empty($group) && !empty($connections)) {
            $this->setConnections($group, $connections);
        }
    }

    /**
     * @param string $group
     * @return Connection
     */
    public function nextConnection(string $group)
    {
        return $this->selector->select($this->connectionGroups[$group]);
    }

    /**
     * @param string $group
     * @return ConnectionPoolContract|void
     */
    public function deathConnection(string $group)
    {
        foreach ($this->connectionGroups[$group] as $key => $connection) {
            if ($connection->isAlive() === false) {
                $connection->connectionFailure();
                $groupKey = "{$group}.{$key}";
                $this->addDeathConnectionGroup($groupKey, $connection);
                $this->offsetUnset($groupKey);
            }
        }
    }

    /**
     * @param string $group
     * @param array $connections
     * @return ConnectionPoolContract|void
     */
    public function setConnections(string $group, array $connections)
    {
        $this->connectionGroups[$group] = $connections;
    }

    /**
     * @param string $group
     * @param Connection $connection
     * @return ConnectionPoolContract|void
     */
    public function addConnection(string $group, Connection $connection)
    {
        $this->connectionGroups[$group][] = $connection;
    }

    /**
     * @param string $group
     * @return bool
     */
    public function hasConnection(string $group): bool
    {
        return isset($this->connectionGroups[$group]);
    }

    /**
     * @return array
     */
    public function getAllConnections(): array
    {
        return $this->getAllConnections();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return Arr::has($this->connectionGroups, $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return Arr::get($this->connectionGroups, $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return array|void
     */
    public function offsetSet($offset, $value)
    {
        return Arr::set($this->connectionGroups, $offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        Arr::forget($this->connectionGroups, $offset);
    }

    /**
     * @param string $group
     * @param Connection $connection
     */
    protected function addDeathConnectionGroup(string $group, Connection $connection)
    {
        Arr::set($this->deathConnectionGroups, $group, $connection);
    }
}