<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;
use SplQueue;
use RangeException;
use SplObjectStorage;
use RuntimeException;

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
     * @var int
     */
    protected $connectionNumber = 0;

    /**
     * @var int
     */
    protected $connectionTime = 0;

    /**
     * @var array
     */
    protected $config = [
        'max_connection_number' => 10,
        'max_idle_number' => 20,
        'min_idle_number' => 10,
        'timeout' => 1
    ];

    /**
     * ConnectionPool constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config ? array_merge($this->config, $config) : $this->config;
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
     * @return Connection
     */
    public function next(): Connection
    {
        /* @var Connection $connection */
        $connection = $this->effectiveConnection();

        $this->connectionTime = time();
        $this->connectionNumber += 1;

        return $connection;
    }

    /**
     * @param ConnectionFactory $factory
     */
    public function create(ConnectionFactory $factory): void
    {
        $maxNumber = $this->config['min_idle_number'];
        while ($maxNumber) {
            $this->join($factory->make($this));
            $maxNumber -= 1;
        }
    }

    /**
     * @param Connection $connection
     * @return ConnectionPoolContract
     */
    public function join(Connection $connection): ConnectionPoolContract
    {
        if ($this->idleQueues->count() > $this->config['max_idle_number']) {
            throw new RuntimeException('More than the maximum number of idle connections');
        }

        $this->idleQueues->enqueue($connection);

        return $this;
    }

    /**
     * @param Connection $connection
     */
    public function release(Connection $connection): void
    {
        $this->tasks->detach($connection);
        $this->idleQueues->enqueue($connection);
    }

    /**
     * @param Connection $connection
     */
    public function close(Connection $connection): void
    {
        $this->tasks->detach($connection);
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
     * @param null|string $key
     * @return array|mixed
     */
    public function getConfig(?string $key = null)
    {
        if ($key && isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $this->config;
    }

    /**
     * @return int
     */
    public function getConnectionNumber(): int
    {
        return $this->connectionNumber;
    }

    /**
     * @return int
     */
    public function getConnectionTime(): int
    {
        return $this->connectionTime;
    }

    /**
     * @param float $time
     * @return bool
     */
    protected function checkTimeout(float $time): bool
    {
        return microtime(true) - $time > $this->config['timeout'];
    }

    /**
     * @return Connection
     */
    protected function effectiveConnection(): Connection
    {
        if ($this->tasks->count() > $this->config['max_connection_number']) {
            throw new RuntimeException('More than the maximum number of connections');
        }

        $start = microtime(true);
        $this->idleQueues->rewind();

        while ($this->idleQueues->valid()) {
            if ($this->checkTimeout($start)) {
                throw new RuntimeException("Connection timeout");
            }

            /* @var Connection $connection */
            $connection = $this->idleQueues->pop();

            //断线重连机制
            if (!$connection->isAlive()) {
                $connection->reconnection();
            }

            if ($connection->isAlive()) {
                $this->tasks->attach($connection);
                return $connection;
            }

            $this->idleQueues->next();
        }

        throw new RangeException("No valid connections found");
    }
}