<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use RangeException;
use BadMethodCallException;

/**
 * Class ConnectionManager
 * @package CrCms\Foundation\ConnectionPool
 */
class ConnectionManager
{
    /**
     * @var ConnectionFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $pools = [];

    /**
     * @var ConnectionPool
     */
    protected $currentPool;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var
     */
    protected $connection;

    /**
     * ConnectionManager constructor.
     * @param Application $app
     * @param ConnectionFactory $factory
     * @param ConnectionPool $pool
     */
    public function __construct(Application $app, ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * @param null|string $name
     * @return ConnectionManager
     */
    public function connection(?string $name = null): ConnectionManager
    {
        $name = $name ? $name : $this->defaultDriver();

        $this->setCurrentPool($name);

        if (!$this->currentPool->has()) {
            $this->makeConnections($name);
        }

        $this->connection = $this->currentPool->next();

        return $this;
    }

    /**
     * @return void
     */
    /*public function close(): void
    {
        $this->currentPool->close($this->connection);
    }*/

    /**
     * @param string $name
     * @return void
     */
    public function makeConnections(string $name): void
    {
        $maxNumber = $this->currentPool->getConfig('max_idle_number');
        while ($maxNumber) {
            $this->currentPool->join(
                $this->factory->make($this->configuration($name), $this->currentPool)
            );
            $maxNumber -= 1;
        }
    }

    /**
     * @param string $name
     */
    protected function setCurrentPool(string $name)
    {
        if (empty($this->pools[$name])) {
            $this->pools[$name] = $this->app->make('pool.pool', $this->configuration($name)['settings']);
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
            throw new InvalidArgumentException("pool config[{$name}] not found");
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
        if (method_exists($this->connection, $name)) {
            $result = call_user_func_array([$this->connection, $name], $arguments);
            if (!$result instanceof $this->connection) {
                return $result;
            }
            return $this;
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}