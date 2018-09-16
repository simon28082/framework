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
     * @var ConnectionPool
     */
    protected $pool;

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
    public function __construct(Application $app, ConnectionFactory $factory, ConnectionPool $pool)
    {
        $this->app = $app;
        $this->pool = $pool;
        $this->factory = $factory;
    }

    /**
     * @param null|string $name
     * @return ConnectionManager
     */
    public function connection(?string $name = null): ConnectionManager
    {
        $this->group = $name = $name ? $name : $this->defaultDriver();

        if (!$this->pool->has($name)) {
            throw new RangeException("The '{$name}' Exceeded the maximum connection limit");
        }

        $this->connection = $this->pool->next($name);

        return $this;
    }

    public function close(): void
    {
        $this->pool->close($this->group, $this->connection);
    }

    /**
     * @param string $name
     * @return array
     */
    public function makeConnections(string $name)
    {
        return array_map(function ($config) use ($name) {
            return $this->factory->make($config);
        }, $this->configuration($name));
    }

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('client.default');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('client.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("client config[{$name}] not found");
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