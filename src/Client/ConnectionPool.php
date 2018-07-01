<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\ConnectionPool as ConnectionPoolContract;
use ArrayAccess;
use CrCms\Foundation\Client\Contracts\Selector;
use Illuminate\Support\Arr;
use BadMethodCallException;
use UnderflowException;

/**
 * Class ConnectionPool
 * @package CrCms\Foundation\Client
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
    public function nextConnection(string $group): Connection
    {
        $this->deathConnection($group);

        if (empty($this->connectionGroups[$group])) {
            throw new UnderflowException("Connection pool, no connection available");
        }

        return $this->selector->select($group, $this->connectionGroups[$group]);
    }

    /**
     * @param string $group
     * @return ConnectionPoolContract
     */
    protected function deathConnection(string $group): ConnectionPoolContract
    {
        foreach ($this->connectionGroups[$group] as $key => $connection) {
            if ($connection->isAlive() === false) {
                $groupKey = "{$group}.{$key}";
                $this->addDeathConnectionGroup($groupKey, $connection);
                $this->offsetUnset($groupKey);
            }
        }

        return $this;
    }

    /**
     * @param string $group
     * @param array $connections
     * @return ConnectionPoolContract
     */
    public function setConnections(string $group, array $connections): ConnectionPoolContract
    {
        $this->connectionGroups[$group] = $connections;
        return $this;
    }

    /**
     * @param string $group
     * @param Connection $connection
     * @return ConnectionPoolContract
     */
    public function addConnection(string $group, Connection $connection): ConnectionPoolContract
    {
        $this->connectionGroups[$group][] = $connection;
        return $this;
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
     * @param string $group
     * @param Connection $connection
     */
    protected function addDeathConnectionGroup(string $group, Connection $connection)
    {
        Arr::set($this->deathConnectionGroups, $group, $connection);
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
}