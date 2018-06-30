<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\ConnectionPool;
use Illuminate\Foundation\Application;
use InvalidArgumentException;

/**
 * Class ConnectionManager
 * @package CrCms\Foundation\Client
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
     * @return Connection
     */
    public function connection(?string $name = null): Connection
    {
        $name = $name ? $name : $this->defaultDriver();

        if ($this->pool->hasConnection($name)) {
            return $this->pool->nextConnection($name);
        }

        return $this->pool->setConnections(
            $name, $this->makeConnections($name)
        )->nextConnection($name);

        /*if (!empty($this->connections[$name])) {
            return $this->connections[$name];
        }

        if ($this->pool->hasConnection($name)) {
            $this->connection = $this->pool->nextConnection($name);
        } else {
            $this->connection = $this->pool->setConnections(
                $name, $this->makeConnections($name)
            )->nextConnection($name);
        }*/
    }

    /**
     * @param string $name
     * @return array
     */
    protected function makeConnections(string $name)
    {
        return array_map(function ($config) use ($name) {
            return $this->factory->make($name, $config);
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

    /*public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connction(), $name)) {
            return call_user_func_array([$this->connect, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }*/
}