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
use BadMethodCallException;

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
     * ConnectionManager constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param ConnectionFactory $factory
     * @param null|string $name
     * @return ConnectionManager
     */
    public function connection(ConnectionFactory $factory, ?string $name = null)
    {
        $name = $name ? $name : $this->defaultDriver();

        $this->setPool($name, $factory);

        /*if (!$this->pool->has()) {
            //$this->max
        }*/

        $this->connection = $this->pool->connection();

        return $this->connection;

        return $this;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return ConnectionPool
     */
    public function getPool(): ConnectionPool
    {
        return $this->pool;
    }

    /**
     * @return void
     */
    public function close(): void
    {
        $this->pool->release($this->connection);
    }

    /**
     * @param ConnectionFactory $factory
     * @return void
     */
    protected function makeConnections(ConnectionPool $pool, ConnectionFactory $factory): void
    {
        $maxNumber = $pool->getConfig('max_idle_number');
        while ($maxNumber) {
            $pool->join($factory->make($pool));
            $maxNumber -= 1;
        }
    }

    /**
     * @param string $name
     * @return void
     */
    protected function setPool(string $name, ConnectionFactory $factory): void
    {
        if (empty($this->pools[$name])) {
            $this->pools[$name] = $this->app->make('pool.pool', $this->configuration($name));
            $this->makeConnections($this->pools[$name],$factory);
        }

        $this->pool = $this->pools[$name];
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
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('pool.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("Pool config[{$name}] not found");
        }

        return $connections[$name];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|mixed
     */
    public function __call(string $name, array $arguments)
    {
        //需要在Manager中管理连接对象，每次都是通过Manager调用连接池，链接池有__call方法来调用
        /*if (method_exists($this->pool, $name)) {
            $result = call_user_func_array([$this->connection, $name], $arguments);
            if (!$result instanceof $this->connection) {
                return $result;
            }
            return $this;
        }*/
        if ($this->connection instanceof Connection) {
            return call_user_func_array([$this->connection, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}