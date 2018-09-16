<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;
use ArrayAccess;
use CrCms\Foundation\ConnectionPool\Contracts\Selector;
use Illuminate\Support\Arr;
use BadMethodCallException;
use UnderflowException;
use SplQueue;
use DomainException;

/**
 * Class ConnectionPool
 * @package CrCms\Foundation\ConnectionPool
 */
class ConnectionPool implements ConnectionPoolContract
{

    /**
     * @var array
     */
    protected $deadGroups = [];

    /**
     * @var array
     */
    protected $activeGroups = [];

    protected $services = [];

    protected $currentConnectionNumber = 0;
    /**
     * @var SplQueue
     *
     */
    protected $queue;

    protected $maxActive;

    protected $maxConnection;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /*public function service(string $service, string $key = 'queue')
    {
        if (!in_array($key, ['maxActive', 'queue'], true)) {
            throw new DomainException("the service");
        }
        return $this->services[$service];
        $this->queue = new SplQueue();
    }*/

    public function has(): bool
    {
        return !$this->queue->isEmpty();
        //return !empty($this->activeGroups[$group]);
    }

    public function next(): Connection
    {


        /* @var Connection $connection */
        $connection = $this->queue->dequeue();

        if (!$connection->isAlive()) {
            $connection->reconnection();
        }

        $this->currentConnectionNumber += 1;

        return $connection;

        //$this->selector->select($group, $this->activeGroups[$group], $this);
    }

    public function join(Connection $connection)
    {
        // TODO: Implement join() method.
    }

    public function release(Connection $connection)
    {
        // TODO: Implement release() method.
    }

    public function close(Connection $connection): void
    {
        // TODO: Implement close() method.
    }



//    public function create(string $group, array $connections): ConnectionPoolContract
//    {
//        $this->activeGroups[$group] = $connections;
//
//        return $this;
//    }
//
//    public function group(string $group): array
//    {
//        return $this->activeGroups[$group] ?? [];
//    }
//
//    public function close(string $group, Connection $connection): void
//    {
//        $connection->close();
//        $this->activeGroups[$group] = $this->activeGroups[$group] ?? [];
//        array_unshift($this->activeGroups[$group], $connection);
//    }
//
//
//    /**
//     * @param string $group
//     * @return Connection
//     */
//    public function nextConnection(string $group): Connection
//    {
//        return $this->selector->select($group, $this->connectionGroups[$group], $this);
//    }
//
//    /**
//     * @param string $group
//     * @return ConnectionPoolContract
//     */
//    protected function deathConnection(string $group): ConnectionPoolContract
//    {
//        if (empty($this->connectionGroups[$group])) {
//            return $this;
//        }
//
//        $isDeath = false;
//
//        foreach ($this->connectionGroups[$group] as $key => $connection) {
//            if ($connection->isAlive() === false) {
//                $isDeath = true;
//                $groupKey = "{$group}.{$key}";
//                $this->addDeathConnectionGroup($groupKey, $connection);
//                $this->offsetUnset($groupKey);
//            }
//        }
//
//        if ($isDeath) {
//            $this->resetConnectionGroups($group);
//        }
//
//        return $this;
//    }
//
//    /**
//     * @param string $group
//     * @return $this
//     */
//    protected function resetConnectionGroups(string $group)
//    {
//        $this->connectionGroups[$group] = array_values($this->connectionGroups[$group]);
//
//        return $this;
//    }
//
//
//    /**
//     * @param string $group
//     * @param array $connections
//     * @return ConnectionPoolContract
//     */
//    public function setConnections(string $group, array $connections): ConnectionPoolContract
//    {
//        $this->connectionGroups[$group] = $connections;
//        return $this;
//    }
//
//    /**
//     * @param string $group
//     * @param Connection $connection
//     * @return ConnectionPoolContract
//     */
//    public function addConnection(string $group, Connection $connection): ConnectionPoolContract
//    {
//        $this->connectionGroups[$group][] = $connection;
//        return $this;
//    }
//
//    /**
//     * @param string $group
//     * @return bool
//     */
//    public function hasConnection(string $group): bool
//    {
//        $this->deathConnection($group);
//
//        return !empty($this->connectionGroups[$group]);
//    }
//
//    /**
//     * @param string $group
//     * @param Connection $connection
//     */
//    protected function addDeathConnectionGroup(string $group, Connection $connection)
//    {
//        Arr::set($this->deathConnectionGroups, $group, $connection);
//    }
//
//    /**
//     * @return array
//     */
//    public function getAllConnections(): array
//    {
//        return $this->getAllConnections();
//    }

}