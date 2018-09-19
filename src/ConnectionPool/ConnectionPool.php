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
use SplQueue;
use SplObjectStorage;
use OutOfBoundsException;

/**
 * Class ConnectionPool
 * @package CrCms\Foundation\ConnectionPool
 */
class ConnectionPool implements ConnectionPoolContract
{
    /**
     * @var SplQueue
     */
    protected $idleQueues;

    /**
     * @var SplObjectStorage
     */
    protected $tasks;

    /**
     * ConnectionPool constructor.
     */
    public function __construct()
    {
        $this->idleQueues = new SplQueue();
        $this->tasks = new SplObjectStorage();
    }

    /**
     * @return bool
     */
    public function has(): bool
    {
        return !$this->idleQueues->isEmpty();
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function put(Connection $connection): void
    {
        $this->idleQueues->enqueue($connection);
    }

    /**
     * @return Connection
     */
    public function get(): Connection
    {
        if ($this->idleQueues->count() > 0) {
            $connection = $this->idleQueues->pop();
            $this->tasks->attach($connection);
            return $connection;
        }

        throw new OutOfBoundsException("Not found connection");
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function destroy(Connection $connection): void
    {
        $this->tasks->detach($connection);
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function release(Connection $connection): void
    {
        $this->tasks->detach($connection);
        $this->idleQueues->enqueue($connection);
    }

    /**
     * @return SplObjectStorage
     */
    public function getTasks(): SplObjectStorage
    {
        return $this->tasks;
    }

    /**
     * @return SplQueue
     */
    public function getIdleQueues(): SplQueue
    {
        return $this->idleQueues;
    }

    /**
     * @return int
     */
    public function getTasksCount(): int
    {
        return $this->tasks->count();
    }

    /**
     * @return int
     */
    public function getIdleQueuesCount(): int
    {
        return $this->idleQueues->count();
    }
}