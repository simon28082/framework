<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use RuntimeException;
use RangeException;

/**
 * Class ConnectionManager
 * @package CrCms\Foundation\ConnectionPool
 */
class ConnectionManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $pools = [];

    /**
     * @var ConnectionPool
     */
    protected $pool;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var ConnectionFactory
     */
    protected $factory;

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
    protected $poolConfig = [
        'max_idle_number' => 500,//最大空闲数
        'min_idle_number' => 50,//最小空闲数
        'max_connection_number' => 400,//最大连接数
        'max_connection_time' => 2,//最大连接时间
    ];

    /**
     * ConnectionManager constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return ConnectionPool
     */
    public function getPool(): ConnectionPool
    {
        return $this->pool;
    }

    /**
     * @param ConnectionFactory $factory
     * @param null|array|string $name
     * @return Connection
     */
    public function connection(ConnectionFactory $factory, $name = null)
    {
        if (is_array($name)) {
            list($name, $configure) = [$name['name'], $name];
        } else {
            $name = $name ? $name : $this->defaultDriver();
            $configure = $this->configuration($name);
        }

        //连接准备
        $this->connectionReady($name ? $name : $this->defaultDriver(), $configure, $factory);

        //连接记录
        $this->connectionRecord();

        //连接回收，超时检测
        $this->connectionRecycling();

        return $this->effectiveConnection();
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function close(Connection $connection): void
    {
        if ($connection->isRelease() || $connection->isAlive()) {
            $connection->makeRecycling();
            $this->pool->release($connection);
        } else {
            $this->pool->destroy($connection);
        }
    }

    /**
     * @return Connection
     */
    protected function effectiveConnection(): Connection
    {
        if ($this->pool->getTasksCount() > $this->poolConfig['max_connection_number']) {
            throw new RuntimeException('More than the maximum number of connections');
        }

        if (!$this->pool->has()) {
            $this->makeConnections($this->pool);
        }

        while ($this->pool->has()) {

            $connection = $this->pool->get();

            //断线重连机制
            if (!$connection->isAlive()) {
                $connection->reconnection();
            }

            //二次重连失败，直接销毁
            if (!$connection->isAlive()) {
                $this->pool->destroy($connection);
                continue;
            }

            return $connection;
        }

        throw new RangeException("No valid connections found");
    }

    /**
     * 连接准备
     *
     * @param string $name
     * @param array $configure
     * @param ConnectionFactory $factory
     */
    protected function connectionReady(string $name, array $configure, ConnectionFactory $factory)
    {
        $this->factory = $factory;
        $this->poolConfig = array_merge($this->poolConfig, $configure);
        $this->pool = $this->pool($name);
    }

    /**
     * 任务连接回收
     *
     * @return void
     */
    protected function connectionRecycling(): void
    {
        /* @var Connection $connection */
        $currentTime = time();
        foreach ($this->pool->getTasks() as $connection) {
            if (
                $connection->isRelease() ||
                $currentTime - $connection->getLaseActivityTime() > $this->poolConfig['max_connection_time']
            ) {
                $this->pool->release($connection);
            }

            if (!$connection->isAlive()) {
                $this->pool->destroy($connection);
            }
        }
    }

    /**
     * @return void
     */
    protected function connectionRecord(): void
    {
        $this->connectionTime = time();
        $this->connectionNumber += 1;
    }

    /**
     * @param ConnectionFactory $factory
     * @return void
     */
    protected function makeConnections(ConnectionPool $pool): void
    {
        $count = min(
            $this->poolConfig['max_idle_number'] - $pool->getIdleQueuesCount(),
            $this->poolConfig['min_idle_number'] + $pool->getIdleQueuesCount()
        );

        while ($count) {
            $pool->put($this->factory->make());
            $count -= 1;
        }
    }

    /**
     * @param string $name
     * @return ConnectionPool
     */
    protected function pool(string $name): ConnectionPool
    {
        return empty($this->pools[$name]) ? $this->initPool($name) : $this->pools[$name];
    }

    /**
     * @param string $name
     * @return ConnectionPool
     */
    protected function initPool(string $name): ConnectionPool
    {
        $this->pools[$name] = $this->app->make('pool.pool');
        $this->makeConnections($this->pools[$name]);
        return $this->pools[$name];
    }

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('pool.default');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration($name): array
    {
        $connections = $this->app->make('config')->get('pool.connections');

        if (!isset($connections[$name])) {
            return $this->poolConfig;
            //throw new InvalidArgumentException("Pool config[{$name}] not found");
        }

        return $connections[$name];
    }
}